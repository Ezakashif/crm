<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Services\PermissionRegistry;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RbacTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RbacSeeder::class);
    }

    public function test_admin_has_all_permissions(): void
    {
        $admin = User::factory()->admin()->create();

        $this->assertTrue($admin->hasRole('admin'));
        $this->assertTrue($admin->hasPermission('view.users'));
        $this->assertTrue($admin->hasPermission('view.roles'));
        $this->assertTrue($admin->hasPermission('create.tasks'));
        $this->assertTrue($admin->canAssignTasks());
    }

    public function test_sales_rep_has_limited_task_permissions(): void
    {
        $salesRep = User::factory()->create();

        $this->assertTrue($salesRep->hasRole('sales'));
        $this->assertTrue($salesRep->hasPermission('view.leads'));
        $this->assertTrue($salesRep->hasPermission('view.tasks'));
        $this->assertTrue($salesRep->hasPermission('update.tasks'));
        $this->assertTrue($salesRep->hasPermission('delete.tasks'));
        $this->assertTrue($salesRep->hasPermission('view_own.activity_logs'));
        $this->assertFalse($salesRep->hasPermission('view_all.tasks'));
        $this->assertFalse($salesRep->hasPermission('create.tasks'));
        $this->assertFalse($salesRep->hasPermission('view.users'));
        $this->assertFalse($salesRep->canAssignTasks());
    }

    public function test_manager_can_manage_tasks_but_not_users(): void
    {
        $manager = User::factory()->manager()->create();

        $this->assertTrue($manager->hasRole('manager'));
        $this->assertTrue($manager->hasPermission('create.tasks'));
        $this->assertTrue($manager->hasPermission('delete.tasks'));
        $this->assertTrue($manager->hasPermission('view_all.tasks'));
        $this->assertTrue($manager->canAssignTasks());
        $this->assertFalse($manager->hasPermission('view.users'));
        $this->assertTrue($manager->hasPermission('view.activity_logs'));
    }

    public function test_user_can_have_multiple_roles(): void
    {
        $user = User::factory()->create();
        $managerRole = Role::query()->where('slug', 'manager')->firstOrFail();

        $user->syncRoles([
            ...$user->roles()->pluck('roles.id')->all(),
            $managerRole->id,
        ]);

        $this->assertTrue($user->hasRole('sales'));
        $this->assertTrue($user->hasRole('manager'));
        $this->assertTrue($user->canAssignTasks());
    }

    public function test_sales_rep_cannot_access_user_management(): void
    {
        $salesRep = User::factory()->create();

        $response = $this->actingAs($salesRep)->get(route('users.index'));

        $response->assertForbidden();
    }

    public function test_manager_cannot_access_user_management(): void
    {
        $manager = User::factory()->manager()->create();

        $response = $this->actingAs($manager)->get(route('users.index'));

        $response->assertForbidden();
    }
}
