<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\PermissionRegistrar;
use App\Services\PermissionRegistry;
use Database\Seeders\RbacSeeder;
use Illuminate\Console\Command;

class SyncPermissionsCommand extends Command
{
    protected $signature = 'permissions:sync';

    protected $description = 'Sync permissions and default roles from the RBAC registry';

    public function handle(PermissionRegistry $registry, PermissionRegistrar $registrar): int
    {
        $registry->sync();

        $this->removeManagerRole();
        $this->syncDefaultRoles();

        $registrar->refreshGates();

        $count = $registry->allSlugs()->count();
        $this->info("Synced {$count} permissions from registry.");
        $this->info('Default roles updated. Only Administrator remains a system role.');

        return self::SUCCESS;
    }

    protected function removeManagerRole(): void
    {
        $managerRole = Role::query()->where('slug', 'manager')->first();

        if (! $managerRole) {
            return;
        }

        $salesRole = Role::query()->where('slug', 'sales')->first();

        if (! $salesRole) {
            $salesRole = Role::query()->create(RbacSeeder::ROLES['sales']);
        }

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

        $this->warn('Removed Manager system role.');
    }

    protected function syncDefaultRoles(): void
    {
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
    }
}
