<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Services\PermissionRegistry;
use App\Services\RbacRoleSynchronizer;
use Illuminate\Database\Seeder;

class RbacSeeder extends Seeder
{
    public const ROLES = [
        'admin' => [
            'name' => 'Administrator',
            'description' => 'Full system access.',
            'is_system' => true,
        ],
        'sales' => [
            'name' => 'Sales Representative',
            'description' => 'Standard CRM access for sales staff.',
            'is_system' => false,
        ],
    ];

    public const ROLE_PERMISSIONS = [
        'admin' => '*',
        'sales' => [
            'view.customers', 'create.customers', 'import.customers', 'update.customers', 'delete.customers',
            'view.leads', 'create.leads', 'import.leads', 'update.leads', 'delete.leads', 'convert.leads', 'log.leads',
            'view.tasks', 'change_status.tasks', 'update.tasks', 'delete.tasks',
            'view_own.activity_logs',
            'view.reports', 'export.reports',
        ],
    ];

    public function run(): void
    {
        app(PermissionRegistry::class)->sync();
        app(RbacRoleSynchronizer::class)->syncDefaultRoles();
    }
}
