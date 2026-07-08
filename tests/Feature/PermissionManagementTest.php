<?php

namespace Tests\Feature;

use App\Models\Permission;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PermissionManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RbacSeeder::class);
    }

    public function test_admin_can_view_permissions_index(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get(route('permissions.index'));

        $response->assertOk()
            ->assertSee('Permissions')
            ->assertSee('leads.view');
    }

    public function test_admin_can_create_permission(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->post(route('permissions.store'), [
            'name' => 'View Reports',
            'slug' => 'reports.view',
            'group' => 'reports',
            'description' => 'Access reporting dashboard',
        ]);

        $response->assertRedirect(route('permissions.index'));

        $this->assertDatabaseHas('permissions', [
            'slug' => 'reports.view',
            'group' => 'reports',
        ]);

        $this->assertTrue($admin->fresh()->hasPermission('reports.view'));
    }

    public function test_admin_can_update_permission(): void
    {
        $admin = User::factory()->admin()->create();
        $permission = Permission::query()->where('slug', 'leads.view')->firstOrFail();

        $response = $this->actingAs($admin)->put(route('permissions.update', $permission), [
            'name' => 'View All Leads',
            'slug' => 'leads.view',
            'group' => 'leads',
            'description' => 'Updated description',
        ]);

        $response->assertRedirect(route('permissions.index'));

        $this->assertDatabaseHas('permissions', [
            'id' => $permission->id,
            'name' => 'View All Leads',
        ]);
    }

    public function test_sales_rep_cannot_manage_permissions(): void
    {
        $salesRep = User::factory()->create();

        $response = $this->actingAs($salesRep)->get(route('permissions.index'));

        $response->assertForbidden();
    }
}
