<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BugReportFixesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RbacSeeder::class);
    }

    public function test_sales_rep_can_update_and_delete_own_task(): void
    {
        $salesRep = User::factory()->create();
        $task = Task::factory()->assignedTo($salesRep)->create([
            'created_by' => $salesRep->id,
        ]);

        $this->assertTrue($salesRep->can('update', $task));
        $this->assertTrue($salesRep->can('delete', $task));

        $otherTask = Task::factory()->create();

        $this->assertFalse($salesRep->can('update', $otherTask));
        $this->assertFalse($salesRep->can('delete', $otherTask));
    }

    public function test_view_all_permission_allows_seeing_other_tasks_without_editing(): void
    {
        $viewer = User::factory()->create();
        $salesRole = \App\Models\Role::query()->where('slug', 'sales')->firstOrFail();
        $viewAllPermission = \App\Models\Permission::query()->where('slug', 'view_all.tasks')->firstOrFail();

        $salesRole->permissions()->syncWithoutDetaching([$viewAllPermission->id]);
        $viewer->cachedPermissionSlugs = null;

        $this->assertTrue($viewer->hasPermission('view_all.tasks'));
        $this->assertFalse($viewer->canAssignTasks());

        $ownTask = Task::factory()->assignedTo($viewer)->create();
        $otherUser = User::factory()->create();
        $otherTask = Task::factory()->assignedTo($otherUser)->create();

        $this->assertTrue($viewer->can('view', $otherTask));
        $this->assertFalse($viewer->can('update', $otherTask));
        $this->assertTrue($viewer->can('update', $ownTask));
    }

    public function test_customer_create_is_logged_for_user_activity(): void
    {
        $salesRep = User::factory()->create();

        $this->actingAs($salesRep)->post(route('customers.store'), [
            'name' => 'Logged Customer',
            'email' => 'logged@example.com',
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $salesRep->id,
            'action' => 'customer.created',
        ]);
    }

    public function test_sales_rep_can_view_own_activity_log(): void
    {
        $salesRep = User::factory()->create();

        ActivityLog::create([
            'user_id' => $salesRep->id,
            'action' => 'customer.created',
            'properties' => ['name' => 'Logged Customer'],
        ]);

        ActivityLog::create([
            'user_id' => User::factory()->admin()->create()->id,
            'action' => 'customer.created',
            'properties' => ['name' => 'Admin Customer'],
        ]);

        $response = $this->actingAs($salesRep)->get(route('activity-logs.index'));

        $response->assertOk()
            ->assertSee('My Activity')
            ->assertSee('Created customer Logged Customer')
            ->assertDontSee('Created customer Admin Customer');
    }

    public function test_admin_can_view_all_user_activity_logs(): void
    {
        $admin = User::factory()->admin()->create();
        $salesRep = User::factory()->create();

        ActivityLog::create([
            'user_id' => $salesRep->id,
            'action' => 'customer.created',
            'properties' => ['name' => 'Sales Customer'],
        ]);

        $response = $this->actingAs($admin)->get(route('activity-logs.index'));

        $response->assertOk()
            ->assertSee('Activity Log')
            ->assertSee('Sales Customer');
    }
}
