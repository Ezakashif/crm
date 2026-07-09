<?php

use App\Models\Role;
use App\Services\PermissionRegistry;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        app(PermissionRegistry::class)->sync();

        $adminRole = Role::query()->where('slug', 'admin')->first();

        if ($adminRole) {
            $adminRole->permissions()->sync(
                \App\Models\Permission::query()->pluck('id')->all()
            );
        }
    }

    public function down(): void
    {
        //
    }
};
