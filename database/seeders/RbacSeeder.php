<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Services\PermissionRegistry;
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
            'view.customers', 'create.customers', 'update.customers', 'delete.customers',
            'view.leads', 'create.leads', 'update.leads', 'delete.leads', 'convert.leads', 'log.leads',
            'view.tasks', 'update.tasks', 'delete.tasks',
            'view_own.activity_logs',
        ],
    ];

    public function run(): void
    {
        app(PermissionRegistry::class)->sync();

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
