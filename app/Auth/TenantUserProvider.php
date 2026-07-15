<?php

namespace App\Auth;

use App\Models\User;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class TenantUserProvider extends EloquentUserProvider
{
    /**
     * Retrieve a user by credentials without CompanyScope leakage.
     *
     * @param  array<string, mixed>  $credentials
     */
    public function retrieveByCredentials(array $credentials): ?Authenticatable
    {
        $credentials = array_filter(
            $credentials,
            fn ($key) => ! str_contains($key, 'password'),
            ARRAY_FILTER_USE_KEY,
        );

        if ($credentials === []) {
            return null;
        }

        /** @var User $model */
        $model = $this->createModel();

        $query = $model->newQuery()->withoutCompanyScope();

        foreach ($credentials as $key => $value) {
            $query->where($key, $value);
        }

        return $query->first();
    }
}
