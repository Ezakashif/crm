<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\Lead;
use App\Models\Task;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RbacSeeder::class);
    }

    public function test_authenticated_user_can_view_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Dashboard');
        $response->assertSee('Here's what needs your attention today');
    }

    public function test_dashboard_shows_actionable_metrics_for_sales_user(): void
    {
        $user = User::factory()->create();

        Lead::factory()->create([
            'created_by' => $user->id,
            'status' => 'new',
            'follow_up_date' => today(),
            'source' => 'website',
        ]);

        Lead::factory()->create([
            'created_by' => $user->id,
            'status' => 'won',
            'source' => 'referral',
        ]);

        Lead::factory()->create([
            'created_by' => $user->id,
            'status' => 'lost',
            'source' => 'facebook',
        ]);

        Task::factory()->assignedTo($user)->create([
            'created_by' => $user->id,
            'status' => 'pending',
            'due_date' => now()->subDay(),
            'title' => 'Call overdue prospect',
        ]);

        Customer::factory()->create([
            'created_by' => $user->id,
            'name' => 'Acme Recent Customer',
        ]);

        $loggedLead = Lead::factory()->create([
            'created_by' => $user->id,
            'name' => 'Logged Activity Lead',
            'status' => 'contacted',
        ]);

        ActivityLog::create([
            'user_id' => $user->id,
            'action' => 'lead.created',
            'subject_type' => Lead::class,
            'subject_id' => $loggedLead->id,
            'properties' => ['name' => $loggedLead->name],
            'ip_address' => '127.0.0.1',
        ]);

        $response = $this->actingAs($user)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Today's Follow-ups');
        $response->assertSee('Pending Tasks');
        $response->assertSee('Overdue Tasks');
        $response->assertSee('New Leads');
        $response->assertSee('Won Leads');
        $response->assertSee('Lost Leads');
        $response->assertSee('Lead Conversion Rate');
        $response->assertSee('Monthly Lead Growth');
        $response->assertSee('Lead Source Distribution');
        $response->assertSee('Recent Leads');
        $response->assertSee('Recent Customers');
        $response->assertSee('Recent Activities');
        $response->assertSee('Call overdue prospect');
        $response->assertSee('Acme Recent Customer');
        $response->assertSee('Add Lead');
        $response->assertSee('Add Customer');
        $response->assertDontSee('Add Task');
        $response->assertSee('50.0');
    }

    public function test_dashboard_respects_task_visibility_for_sales_user(): void
    {
        $viewer = User::factory()->create();
        $other = User::factory()->create();

        Task::factory()->assignedTo($other)->create([
            'created_by' => $other->id,
            'status' => 'pending',
            'title' => 'Hidden other task',
            'due_date' => now()->subDays(2),
        ]);

        Task::factory()->assignedTo($viewer)->create([
            'created_by' => $viewer->id,
            'status' => 'pending',
            'title' => 'Visible own task',
            'due_date' => now()->subDay(),
        ]);

        $response = $this->actingAs($viewer)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Visible own task');
        $response->assertDontSee('Hidden other task');
    }

    public function test_manager_sees_quick_actions_they_can_perform(): void
    {
        $manager = User::factory()->manager()->create();

        $response = $this->actingAs($manager)->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Add Lead');
        $response->assertSee('Add Task');
        $response->assertSee('Add Customer');
        $response->assertSee('Lead Board');
        $response->assertSee('Task Board');
    }

    public function test_guest_is_redirected_from_dashboard(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
    }
}
