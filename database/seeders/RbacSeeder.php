<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RbacSeeder extends Seeder
{
    public const PERMISSIONS = [
        ['name' => 'View Customers', 'slug' => 'customers.view', 'group' => 'customers'],
        ['name' => 'Create Customers', 'slug' => 'customers.create', 'group' => 'customers'],
        ['name' => 'Update Customers', 'slug' => 'customers.update', 'group' => 'customers'],
        ['name' => 'Delete Customers', 'slug' => 'customers.delete', 'group' => 'customers'],
        ['name' => 'View Leads', 'slug' => 'leads.view', 'group' => 'leads'],
        ['name' => 'Create Leads', 'slug' => 'leads.create', 'group' => 'leads'],
        ['name' => 'Update Leads', 'slug' => 'leads.update', 'group' => 'leads'],
        ['name' => 'Delete Leads', 'slug' => 'leads.delete', 'group' => 'leads'],
        ['name' => 'Convert Leads', 'slug' => 'leads.convert', 'group' => 'leads'],
        ['name' => 'Create Lead Activities', 'slug' => 'leads.activities.create', 'group' => 'leads'],
        ['name' => 'View Tasks', 'slug' => 'tasks.view', 'group' => 'tasks'],
        ['name' => 'Create Tasks', 'slug' => 'tasks.create', 'group' => 'tasks'],
        ['name' => 'Update Tasks', 'slug' => 'tasks.update', 'group' => 'tasks'],
        ['name' => 'Delete Tasks', 'slug' => 'tasks.delete', 'group' => 'tasks'],
        ['name' => 'Assign Tasks', 'slug' => 'tasks.assign', 'group' => 'tasks'],
        ['name' => 'Manage Users', 'slug' => 'users.manage', 'group' => 'admin'],
        ['name' => 'Manage Roles', 'slug' => 'roles.manage', 'group' => 'admin'],
        ['name' => 'Manage Permissions', 'slug' => 'permissions.manage', 'group' => 'admin'],
        ['name' => 'View Activity Logs', 'slug' => 'activity-logs.view', 'group' => 'admin'],
        ['name' => 'Website Lead Demo', 'slug' => 'demo.website-lead', 'group' => 'admin'],
    ];

    public const ROLES = [
        'admin' => [
            'name' => 'Administrator',
            'description' => 'Full system access.',
            'is_system' => true,
        ],
        'manager' => [
            'name' => 'Manager',
            'description' => 'Team oversight with full CRM access except user administration.',
            'is_system' => true,
        ],
        'sales' => [
            'name' => 'Sales Representative',
            'description' => 'Standard CRM access for sales staff.',
            'is_system' => true,
        ],
    ];

    public const ROLE_PERMISSIONS = [
        'admin' => '*',
        'manager' => [
            'customers.view', 'customers.create', 'customers.update', 'customers.delete',
            'leads.view', 'leads.create', 'leads.update', 'leads.delete', 'leads.convert', 'leads.activities.create',
            'tasks.view', 'tasks.create', 'tasks.update', 'tasks.delete', 'tasks.assign',
        ],
        'sales' => [
            'customers.view', 'customers.create', 'customers.update', 'customers.delete',
            'leads.view', 'leads.create', 'leads.update', 'leads.delete', 'leads.convert', 'leads.activities.create',
            'tasks.view', 'tasks.update',
        ],
    ];

    public function run(): void
    {
        foreach (self::PERMISSIONS as $permission) {
            Permission::query()->updateOrCreate(
                ['slug' => $permission['slug']],
                $permission,
            );
        }

        $allPermissionIds = Permission::query()->pluck('id', 'slug');

        foreach (self::ROLES as $slug => $attributes) {
            $role = Role::query()->updateOrCreate(
                ['slug' => $slug],
                $attributes,
            );

            $permissionSlugs = self::ROLE_PERMISSIONS[$slug];

            $permissionIds = $permissionSlugs === '*'
                ? $allPermissionIds->values()->all()
                : collect($permissionSlugs)
                    ->map(fn (string $permissionSlug) => $allPermissionIds[$permissionSlug])
                    ->all();

            $role->permissions()->sync($permissionIds);
        }
    }
}
