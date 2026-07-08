<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
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
        $this->assertTrue($admin->hasPermission('users.manage'));
        $this->assertTrue($admin->hasPermission('tasks.create'));
        $this->assertTrue($admin->canAssignTasks());
    }

    public function test_sales_rep_has_limited_task_permissions(): void
    {
        $salesRep = User::factory()->create();

        $this->assertTrue($salesRep->hasRole('sales'));
        $this->assertTrue($salesRep->hasPermission('leads.view'));
        $this->assertTrue($salesRep->hasPermission('tasks.view'));
        $this->assertTrue($salesRep->hasPermission('tasks.update'));
        $this->assertFalse($salesRep->hasPermission('tasks.create'));
        $this->assertFalse($salesRep->hasPermission('users.manage'));
        $this->assertFalse($salesRep->canAssignTasks());
    }

    public function test_manager_can_manage_tasks_but_not_users(): void
    {
        $manager = User::factory()->manager()->create();

        $this->assertTrue($manager->hasRole('manager'));
        $this->assertTrue($manager->hasPermission('tasks.create'));
        $this->assertTrue($manager->hasPermission('tasks.delete'));
        $this->assertTrue($manager->canAssignTasks());
        $this->assertFalse($manager->hasPermission('users.manage'));
        $this->assertFalse($manager->hasPermission('activity-logs.view'));
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
