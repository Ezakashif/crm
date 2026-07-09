<?php

use App\Services\PermissionRegistry;
use Database\Seeders\RbacSeeder;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        app(PermissionRegistry::class)->sync();

        (new RbacSeeder)->run();
    }

    public function down(): void
    {
        //
    }
};
