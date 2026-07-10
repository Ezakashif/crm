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
    public function search(User $user, ?string $term): array
    {
        $term = trim((string) $term);
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
                ->limit(self::PER_CATEGORY_LIMIT)
                ->get(['id', 'name', 'email', 'phone', 'company', 'status', 'assigned_to'])
            : collect();

        $customers = $canViewCustomers
            ? Customer::query()
                ->search($term)
                ->latest('id')
                ->limit(self::PER_CATEGORY_LIMIT)
                ->get(['id', 'name', 'email', 'phone', 'company_name', 'status'])
            : collect();

        $companies = $this->searchCompanies($user, $term, $canViewLeads, $canViewCustomers);

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
     * Distinct company names matching the term (company field only).
     *
     * @return list<array{name: string, sources: list<string>}>
     */
    protected function searchCompanies(
        User $user,
        string $term,
        bool $canViewLeads,
        bool $canViewCustomers,
    ): array {
        $companies = [];

        if ($canViewLeads) {
            $names = Lead::visibleTo($user)
                ->whereNotNull('leads.company')
                ->where('leads.company', '!=', '')
                ->where('leads.company', 'like', "%{$term}%")
                ->orderBy('leads.company')
                ->limit(self::PER_CATEGORY_LIMIT)
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
                ->limit(self::PER_CATEGORY_LIMIT)
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

        return array_values(array_slice($companies, 0, self::PER_CATEGORY_LIMIT));
    }
}
