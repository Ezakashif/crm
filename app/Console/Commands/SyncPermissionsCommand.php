<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Services\PermissionRegistrar;
use App\Services\PermissionRegistry;
use Illuminate\Console\Command;

class SyncPermissionsCommand extends Command
{
    protected $signature = 'permissions:sync';

    protected $description = 'Sync permissions from config/permissions.php registry';

    public function handle(PermissionRegistry $registry, PermissionRegistrar $registrar): int
    {
        $registry->sync();

        $adminRole = Role::query()->where('slug', 'admin')->first();

        if ($adminRole) {
            $adminRole->permissions()->sync(
                \App\Models\Permission::query()->pluck('id')->all()
            );
        }

        $registrar->refreshGates();

        $count = $registry->allSlugs()->count();
        $this->info("Synced {$count} permissions from registry.");

        return self::SUCCESS;
    }
}
