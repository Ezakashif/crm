<?php

namespace App\Services;

use App\Models\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class CompanyListQueryService
{
    /**
     * @return array<string, mixed>
     */
    public function filterRules(): array
    {
        return [
            'search' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', Rule::in(array_keys(Company::STATUSES))],
            'subscription_status' => ['nullable', Rule::in(array_keys(Company::SUBSCRIPTION_STATUSES))],
            'plan_id' => ['nullable', 'integer', 'exists:plans,id'],
        ];
    }

    /**
     * @param  array{search?: string|null, status?: string|null, subscription_status?: string|null, plan_id?: int|null}  $filters
     * @return Builder<Company>
     */
    public function query(array $filters): Builder
    {
        return Company::query()
            ->with([
                'owner:id,name,email',
                'plan:id,name,slug',
            ])
            ->withCount(['users', 'leads', 'customers', 'tasks'])
            ->when(filled($filters['search'] ?? null), function ($query) use ($filters) {
                $term = $filters['search'];
                $query->where(function ($builder) use ($term) {
                    $builder->where('name', 'like', "%{$term}%")
                        ->orWhere('slug', 'like', "%{$term}%")
                        ->orWhere('email', 'like', "%{$term}%")
                        ->orWhereHas('owner', function ($ownerQuery) use ($term) {
                            $ownerQuery->withoutCompanyScope()
                                ->where(function ($inner) use ($term) {
                                    $inner->where('name', 'like', "%{$term}%")
                                        ->orWhere('email', 'like', "%{$term}%");
                                });
                        });
                });
            })
            ->when(filled($filters['status'] ?? null), fn ($query) => $query->where('status', $filters['status']))
            ->when(filled($filters['subscription_status'] ?? null), function ($query) use ($filters) {
                if ($filters['subscription_status'] === Company::SUBSCRIPTION_EXPIRED) {
                    $query->subscriptionExpired();

                    return;
                }

                if ($filters['subscription_status'] === Company::SUBSCRIPTION_TRIAL) {
                    $query->onTrial();

                    return;
                }

                $query->where('subscription_status', $filters['subscription_status']);
            })
            ->when(filled($filters['plan_id'] ?? null), fn ($query) => $query->where('plan_id', $filters['plan_id']))
            ->latest();
    }
}
