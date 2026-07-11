<?php

namespace App\Models\Concerns;

use App\Models\Company;
use App\Models\Scopes\CompanyScope;
use App\Support\CurrentCompany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @mixin Model
 */
trait BelongsToCompany
{
    public static function bootBelongsToCompany(): void
    {
        static::addGlobalScope(new CompanyScope);

        static::creating(function (Model $model): void {
            if ($model->getAttribute('company_id') !== null) {
                return;
            }

            $companyId = app(CurrentCompany::class)->id();

            if ($companyId !== null) {
                $model->setAttribute('company_id', $companyId);
            }
        });
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function scopeForCompany(Builder $query, Company|int $company): Builder
    {
        $companyId = $company instanceof Company ? $company->id : $company;

        return $query->where($query->getModel()->getTable().'.company_id', $companyId);
    }

    public function scopeWithoutCompanyScope(Builder $query): Builder
    {
        return $query->withoutGlobalScope(CompanyScope::class);
    }
}
