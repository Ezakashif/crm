<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class GlobalSearchTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RbacSeeder::class);
    }

    public function test_guest_is_redirected_from_search(): void
    {
        $this->get(route('search.index', ['q' => 'acme']))
            ->assertRedirect(route('login'));
    }

    public function test_sales_user_can_search_assigned_leads_and_customers(): void
    {
        $viewer = User::factory()->create(['name' => 'Viewer Sales']);
        $other = User::factory()->create(['name' => 'Other Sales']);

        Lead::factory()->assignedTo($viewer)->create([
            'created_by' => $viewer->id,
            'name' => 'Alpha Lead',
            'email' => 'alpha@example.com',
            'phone' => '111-222-3333',
            'company' => 'Acme Robotics',
        ]);

        Lead::factory()->assignedTo($other)->create([
            'created_by' => $other->id,
            'name' => 'Hidden Lead',
            'email' => 'hidden@example.com',
            'company' => 'Acme Robotics',
        ]);

        Customer::factory()->create([
            'created_by' => $viewer->id,
            'name' => 'Beta Customer',
            'email' => 'beta@example.com',
            'company_name' => 'Acme Robotics',
        ]);

        $response = $this->actingAs($viewer)->get(route('search.index', ['q' => 'Acme']));

        $response->assertOk();
        $response->assertSee('Alpha Lead');
        $response->assertDontSee('Hidden Lead');
        $response->assertSee('Beta Customer');
        $response->assertSee('Acme Robotics');
        $response->assertSee('Leads');
        $response->assertSee('Customers');
        $response->assertSee('Companies');
    }

    public function test_search_matches_email_and_phone(): void
    {
        $user = User::factory()->create();

        Lead::factory()->assignedTo($user)->create([
            'created_by' => $user->id,
            'name' => 'Phone Match Lead',
            'email' => 'unique-search@example.com',
            'phone' => '555-0199',
            'company' => 'Other Co',
        ]);

        $this->actingAs($user)
            ->get(route('search.index', ['q' => 'unique-search']))
            ->assertOk()
            ->assertSee('Phone Match Lead');

        $this->actingAs($user)
            ->get(route('search.index', ['q' => '555-0199']))
            ->assertOk()
            ->assertSee('Phone Match Lead');
    }

    public function test_short_query_does_not_run_search(): void
    {
        $user = User::factory()->create();

        Lead::factory()->assignedTo($user)->create([
            'created_by' => $user->id,
            'name' => 'Short Query Lead',
            'company' => 'ZZ',
        ]);

        $this->actingAs($user)
            ->get(route('search.index', ['q' => 'Z']))
            ->assertOk()
            ->assertSee('Enter at least 2 characters')
            ->assertDontSee('Short Query Lead');
    }

    public function test_user_without_crm_view_permissions_is_forbidden(): void
    {
        $user = User::factory()->create();
        $salesRole = Role::query()->where('slug', 'sales')->firstOrFail();
        $salesRole->permissions()->detach();
        $user->cachedPermissionSlugs = null;

        $this->actingAs($user)
            ->get(route('search.index', ['q' => 'acme']))
            ->assertForbidden();
    }

    public function test_user_with_only_customers_permission_sees_customers_not_leads(): void
    {
        $user = User::factory()->create();
        $salesRole = Role::query()->where('slug', 'sales')->firstOrFail();
        $viewLeads = Permission::query()->where('slug', 'view.leads')->firstOrFail();
        $salesRole->permissions()->detach([$viewLeads->id]);
        $user->cachedPermissionSlugs = null;

        Lead::factory()->assignedTo($user)->create([
            'created_by' => $user->id,
            'name' => 'Should Not Appear Lead',
            'company' => 'Shared Co',
        ]);

        Customer::factory()->create([
            'created_by' => $user->id,
            'name' => 'Visible Customer',
            'company_name' => 'Shared Co',
        ]);

        $response = $this->actingAs($user)->get(route('search.index', ['q' => 'Shared']));

        $response->assertOk();
        $response->assertSee('Visible Customer');
        $response->assertDontSee('Should Not Appear Lead');
        $response->assertSee('Companies');
    }

    public function test_admin_sees_unassigned_leads_in_search(): void
    {
        $admin = User::factory()->admin()->create();

        Lead::factory()->create([
            'created_by' => $admin->id,
            'assigned_to' => null,
            'name' => 'Unassigned Search Lead',
            'company' => 'Orphan Corp',
        ]);

        $this->actingAs($admin)
            ->get(route('search.index', ['q' => 'Orphan']))
            ->assertOk()
            ->assertSee('Unassigned Search Lead');
    }

    public function test_suggest_returns_categorized_json_for_assigned_leads(): void
    {
        $viewer = User::factory()->create();
        $other = User::factory()->create();

        $lead = Lead::factory()->assignedTo($viewer)->create([
            'created_by' => $viewer->id,
            'name' => 'Suggestable Lead',
            'email' => 'suggestable@example.com',
            'company' => 'Suggest Co',
        ]);

        Lead::factory()->assignedTo($other)->create([
            'created_by' => $other->id,
            'name' => 'Other Suggest Lead',
            'company' => 'Suggest Co',
        ]);

        Customer::factory()->create([
            'created_by' => $viewer->id,
            'name' => 'Suggestable Customer',
            'company_name' => 'Suggest Co',
        ]);

        $response = $this->actingAs($viewer)->getJson(route('search.suggest', ['q' => 'Suggest']));

        $response->assertOk();
        $response->assertJsonPath('too_short', false);
        $response->assertJsonFragment(['title' => 'Suggestable Lead']);
        $response->assertJsonFragment(['title' => 'Suggestable Customer']);
        $response->assertJsonFragment(['title' => 'Suggest Co']);
        $response->assertJsonMissing(['title' => 'Other Suggest Lead']);
        $response->assertJsonFragment(['url' => route('leads.show', $lead)]);
    }

    public function test_suggest_returns_empty_groups_for_short_query(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->getJson(route('search.suggest', ['q' => 'a']))
            ->assertOk()
            ->assertJsonPath('too_short', true)
            ->assertJsonPath('groups', []);
    }
}
