<?php

namespace Tests\Feature;

use App\Models\ActivityLog;
use App\Models\Company;
use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CompanySettingsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RbacSeeder::class);
    }

    public function test_company_admin_can_update_its_own_settings_and_activity_is_logged(): void
    {
        $company = Company::factory()->create();
        $admin = User::factory()->admin()->create(['company_id' => $company->id]);

        $this->actingAs($admin)
            ->patch(route('company.settings.update'), [
                'name' => 'Acme CRM',
                'email' => 'hello@acme.test',
                'phone' => '+1 555 0100',
                'city' => 'Austin',
                'country' => 'US',
                'timezone' => 'America/Chicago',
                'currency' => 'USD',
                'business_hours' => ['monday' => '09:00–17:00'],
            ])
            ->assertRedirect()
            ->assertSessionHas('success');

        $this->assertDatabaseHas('companies', [
            'id' => $company->id,
            'name' => 'Acme CRM',
            'city' => 'Austin',
            'timezone' => 'America/Chicago',
        ]);
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'company.settings_updated',
            'company_id' => $company->id,
            'user_id' => $admin->id,
        ]);
    }

    public function test_sales_user_cannot_access_company_settings(): void
    {
        $company = Company::factory()->create();
        $sales = User::factory()->create(['company_id' => $company->id, 'role' => 'sales']);
        $sales->syncRolesFromLegacyColumn();

        $this->actingAs($sales)
            ->get(route('company.settings.edit'))
            ->assertForbidden();
    }
}
