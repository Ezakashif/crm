<?php

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\PermissionRegistry;
use Database\Seeders\RbacSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        app(PermissionRegistry::class)->sync();

        $permissionsBySlug = Permission::query()->pluck('id', 'slug');
        $salesRole = Role::query()->where('slug', 'sales')->first();
        $managerRole = Role::query()->where('slug', 'manager')->first();

        if ($managerRole && $salesRole) {
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

        foreach (RbacSeeder::ROLES as $slug => $attributes) {
            Role::query()->updateOrCreate(
                ['slug' => $slug],
                $attributes,
            );
        }

        foreach (RbacSeeder::ROLE_PERMISSIONS as $slug => $permissionSlugs) {
            $role = Role::query()->where('slug', $slug)->first();

            if (! $role) {
                continue;
            }

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
    }

    public function down(): void
    {
        //
    }
};
