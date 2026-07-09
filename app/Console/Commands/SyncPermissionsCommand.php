<?php

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Role;
use App\Services\PermissionRegistrar;
use App\Services\PermissionRegistry;
use Database\Seeders\RbacSeeder;
use Illuminate\Console\Command;

class SyncPermissionsCommand extends Command
{
    protected $signature = 'permissions:sync';

    protected $description = 'Sync permissions from config/permissions.php registry';

    public function handle(PermissionRegistry $registry, PermissionRegistrar $registrar): int
    {
        $registry->sync();

        $permissionsBySlug = Permission::query()->pluck('id', 'slug');

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

            $role->permissions()->syncWithoutDetaching($permissionIds);
        }

        $registrar->refreshGates();

        $count = $registry->allSlugs()->count();
        $this->info("Synced {$count} permissions from registry.");

        return self::SUCCESS;
    }
}
