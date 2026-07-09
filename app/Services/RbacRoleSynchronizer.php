<?php

namespace App\Services;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RbacSeeder;

class RbacRoleSynchronizer
{
    /**
     * Ensure default roles match the registry:
     * - only Administrator is a system role
     * - Manager is removed
     * - Sales is demoted to a custom/default role
     */
    public function syncDefaultRoles(): void
    {
        $this->removeManagerRole();

        $permissionsBySlug = Permission::query()->pluck('id', 'slug');

        foreach (RbacSeeder::ROLES as $slug => $attributes) {
            $role = Role::query()->updateOrCreate(
                ['slug' => $slug],
                $attributes,
            );

            $permissionSlugs = RbacSeeder::ROLE_PERMISSIONS[$slug];

            if ($permissionSlugs === '*') {
                $role->permissions()->sync($permissionsBySlug->values()->all());

                continue;
            }

            $permissionIds = collect($permissionSlugs)
                ->filter(fn (string $permissionSlug) => $permissionsBySlug->has($permissionSlug))
                ->map(fn (string $permissionSlug) => $permissionsBySlug[$permissionSlug])
                ->all();

            $role->permissions()->sync($permissionIds);
        }

        // Hard guarantee: no leftover system flags on non-admin roles.
        Role::query()
            ->where('slug', '!=', 'admin')
            ->where('is_system', true)
            ->update(['is_system' => false]);
    }

    public function removeManagerRole(): void
    {
        $managerRole = Role::query()->where('slug', 'manager')->first();

        if (! $managerRole) {
            return;
        }

        $salesRole = Role::query()->updateOrCreate(
            ['slug' => 'sales'],
            RbacSeeder::ROLES['sales'],
        );

        $managerUserIds = $managerRole->users()->pluck('users.id');

        foreach ($managerUserIds as $userId) {
            $user = User::query()->find($userId);

            if (! $user) {
                continue;
            }

            $roleIds = $user->roles()
                ->where('roles.slug', '!=', 'manager')
                ->pluck('roles.id')
                ->all();

            if (! in_array($salesRole->id, $roleIds, true) && ! $user->hasRole('admin')) {
                $roleIds[] = $salesRole->id;
            }

            $user->syncRoles($roleIds);
        }

        $managerRole->permissions()->detach();
        $managerRole->users()->detach();
        $managerRole->delete();
    }
}
