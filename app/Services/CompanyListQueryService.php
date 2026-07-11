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
        ];
    }

    /**
     * @param  array{search?: string|null, status?: string|null}  $filters
     * @return Builder<Company>
     */
    public function query(array $filters): Builder
    {
        return Company::query()
            ->withCount(['users', 'leads', 'customers', 'tasks'])
            ->when(filled($filters['search'] ?? null), function ($query) use ($filters) {
                $term = $filters['search'];
                $query->where(function ($builder) use ($term) {
                    $builder->where('name', 'like', "%{$term}%")
                        ->orWhere('slug', 'like', "%{$term}%");
                });
            })
            ->when(filled($filters['status'] ?? null), fn ($query) => $query->where('status', $filters['status']))
            ->latest();
    }
}
