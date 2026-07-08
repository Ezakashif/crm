<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Gate;

class PermissionRegistrar
{
    public function registerGates(): void
    {
        try {
            $permissions = Permission::query()->pluck('slug');
        } catch (\Throwable) {
            return;
        }

        foreach ($permissions as $slug) {
            Gate::define($slug, fn (User $user) => $user->hasPermission($slug));
        }

        Gate::define('manage-users', fn (User $user) => $user->hasPermission('users.manage'));
    }
}
