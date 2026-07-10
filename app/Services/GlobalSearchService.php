<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Support\Collection;

class GlobalSearchService
{
    public const MIN_TERM_LENGTH = 2;

    public const PER_CATEGORY_LIMIT = 8;

    public const SUGGEST_LIMIT = 5;

    /**
     * Run a permission-aware global search across leads, customers, and companies.
     *
     * Reuses Lead::visibleTo / Lead::search and Customer::search — no duplicated match logic.
     *
     * @return array{
     *     term: string,
     *     too_short: bool,
     *     can_view_leads: bool,
     *     can_view_customers: bool,
     *     leads: Collection<int, Lead>,
     *     customers: Collection<int, Customer>,
     *     companies: list<array{name: string, sources: list<string>}>,
     *     total: int
     * }
     */
    public function search(User $user, ?string $term, ?int $limit = null): array
    {
        $term = trim((string) $term);
        $limit = $limit ?? self::PER_CATEGORY_LIMIT;
        $canViewLeads = $user->hasPermission('view.leads');
        $canViewCustomers = $user->hasPermission('view.customers');

        $empty = [
            'term' => $term,
            'too_short' => mb_strlen($term) < self::MIN_TERM_LENGTH,
            'can_view_leads' => $canViewLeads,
            'can_view_customers' => $canViewCustomers,
            'leads' => collect(),
            'customers' => collect(),
            'companies' => [],
            'total' => 0,
        ];

        if ($empty['too_short'] || (! $canViewLeads && ! $canViewCustomers)) {
            return $empty;
        }

        $leads = $canViewLeads
            ? Lead::visibleTo($user)
                ->search($term)
                ->with('assignee:id,name')
                ->latest('id')
                ->limit($limit)
                ->get(['id', 'name', 'email', 'phone', 'company', 'status', 'assigned_to'])
            : collect();

        $customers = $canViewCustomers
            ? Customer::query()
                ->search($term)
                ->latest('id')
                ->limit($limit)
                ->get(['id', 'name', 'email', 'phone', 'company_name', 'status'])
            : collect();

        $companies = $this->searchCompanies($user, $term, $canViewLeads, $canViewCustomers, $limit);

        return [
            'term' => $term,
            'too_short' => false,
            'can_view_leads' => $canViewLeads,
            'can_view_customers' => $canViewCustomers,
            'leads' => $leads,
            'customers' => $customers,
            'companies' => $companies,
            'total' => $leads->count() + $customers->count() + count($companies),
        ];
    }

    /**
     * Compact JSON payload for navbar / inline typeahead suggestions.
     *
     * @return array{
     *     term: string,
     *     too_short: bool,
     *     groups: list<array{type: string, label: string, items: list<array{id: int|string, title: string, subtitle: string, url: string}>}>
     * }
     */
    public function suggest(User $user, ?string $term): array
    {
        $payload = $this->search($user, $term, self::SUGGEST_LIMIT);
        $groups = [];

        if ($payload['can_view_leads'] && $payload['leads']->isNotEmpty()) {
            $groups[] = [
                'type' => 'leads',
                'label' => 'Leads',
                'items' => $payload['leads']->map(fn (Lead $lead) => [
                    'id' => $lead->id,
                    'title' => $lead->name,
                    'subtitle' => $this->joinSubtitle([
                        $lead->email,
                        $lead->phone,
                        $lead->company,
                    ]),
                    'url' => route('leads.show', $lead),
                ])->values()->all(),
            ];
        }

        if ($payload['can_view_customers'] && $payload['customers']->isNotEmpty()) {
            $groups[] = [
                'type' => 'customers',
                'label' => 'Customers',
                'items' => $payload['customers']->map(fn (Customer $customer) => [
                    'id' => $customer->id,
                    'title' => $customer->name,
                    'subtitle' => $this->joinSubtitle([
                        $customer->email,
                        $customer->phone,
                        $customer->company_name,
                    ]),
                    'url' => $user->hasPermission('update.customers')
                        ? route('customers.edit', $customer)
                        : route('customers.index', ['search' => $customer->name]),
                ])->values()->all(),
            ];
        }

        if (($payload['can_view_leads'] || $payload['can_view_customers']) && $payload['companies'] !== []) {
            $groups[] = [
                'type' => 'companies',
                'label' => 'Companies',
                'items' => collect($payload['companies'])->map(function (array $company) {
                    $sources = $company['sources'];
                    $url = in_array('leads', $sources, true)
                        ? route('leads.index', ['search' => $company['name']])
                        : route('customers.index', ['search' => $company['name']]);

                    return [
                        'id' => mb_strtolower($company['name']),
                        'title' => $company['name'],
                        'subtitle' => 'Found in '.implode(' & ', array_map('ucfirst', $sources)),
                        'url' => $url,
                    ];
                })->values()->all(),
            ];
        }

        return [
            'term' => $payload['term'],
            'too_short' => $payload['too_short'],
            'groups' => $groups,
        ];
    }

    /**
     * @param  list<string|null>  $parts
     */
    protected function joinSubtitle(array $parts): string
    {
        return collect($parts)
            ->filter(fn ($part) => filled($part))
            ->implode(' · ');
    }

    /**
     * Distinct company names matching the term (company field only).
     *
     * @return list<array{name: string, sources: list<string>}>
     */
    protected function searchCompanies(
        User $user,
        string $term,
        bool $canViewLeads,
        bool $canViewCustomers,
        int $limit = self::PER_CATEGORY_LIMIT,
    ): array {
        $companies = [];

        if ($canViewLeads) {
            $names = Lead::visibleTo($user)
                ->whereNotNull('leads.company')
                ->where('leads.company', '!=', '')
                ->where('leads.company', 'like', "%{$term}%")
                ->orderBy('leads.company')
                ->limit($limit)
                ->distinct()
                ->pluck('leads.company');

            foreach ($names as $name) {
                $key = mb_strtolower(trim((string) $name));

                if ($key === '') {
                    continue;
                }

                $companies[$key] = [
                    'name' => (string) $name,
                    'sources' => ['leads'],
                ];
            }
        }

        if ($canViewCustomers) {
            $names = Customer::query()
                ->whereNotNull('company_name')
                ->where('company_name', '!=', '')
                ->where('company_name', 'like', "%{$term}%")
                ->orderBy('company_name')
                ->limit($limit)
                ->distinct()
                ->pluck('company_name');

            foreach ($names as $name) {
                $key = mb_strtolower(trim((string) $name));

                if ($key === '') {
                    continue;
                }

                if (isset($companies[$key])) {
                    if (! in_array('customers', $companies[$key]['sources'], true)) {
                        $companies[$key]['sources'][] = 'customers';
                    }

                    continue;
                }

                $companies[$key] = [
                    'name' => (string) $name,
                    'sources' => ['customers'],
                ];
            }
        }

        ksort($companies);

        return array_values(array_slice($companies, 0, $limit));
    }
}
