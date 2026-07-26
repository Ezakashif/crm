<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\SuperAdmin\PlatformSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlatformMarketingContactDetailsTest extends TestCase
{
    use RefreshDatabase;

    public function test_footer_and_contact_page_reflect_super_admin_company_settings(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)
            ->put(route('superadmin.settings.update'), [
                'platform_name' => 'Northline CRM',
                'default_timezone' => 'UTC',
                'default_currency' => 'USD',
                'trial_duration_days' => 14,
                'default_company_status' => 'active',
                'company_email' => 'hello@northline.example',
                'company_phone' => '+1 555 999 1212',
                'company_address' => '500 Harbor Ave, Seattle, WA 98101',
                'company_linkedin_url' => 'https://www.linkedin.com/company/northline',
                'company_facebook_url' => 'https://www.facebook.com/northline',
                'company_twitter_url' => 'https://x.com/northline',
                'company_github_url' => 'https://github.com/northline',
            ])
            ->assertRedirect();

        app(PlatformSettingsService::class)->applyBranding();

        $this->assertSame('hello@northline.example', config('marketing.contact.email'));
        $this->assertSame('+1 555 999 1212', config('marketing.contact.phone'));
        $this->assertSame('500 Harbor Ave, Seattle, WA 98101', config('marketing.contact.address'));
        $this->assertSame('Northline CRM', config('marketing.name'));

        auth()->logout();

        $this->get(route('marketing.contact'))
            ->assertOk()
            ->assertSee('hello@northline.example', false)
            ->assertSee('+1 555 999 1212', false)
            ->assertSee('500 Harbor Ave, Seattle, WA 98101', false)
            ->assertSee('https://www.linkedin.com/company/northline', false)
            ->assertSee('https://www.facebook.com/northline', false)
            ->assertSee('https://x.com/northline', false)
            ->assertSee('https://github.com/northline', false)
            ->assertDontSee('hello@algos.test', false)
            ->assertDontSee('1200 Market Street', false);
    }

    public function test_empty_company_address_is_hidden_from_public_ui(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)
            ->put(route('superadmin.settings.update'), [
                'platform_name' => 'Northline CRM',
                'default_timezone' => 'UTC',
                'default_currency' => 'USD',
                'trial_duration_days' => 14,
                'default_company_status' => 'active',
                'company_email' => 'hello@northline.example',
                'company_phone' => '+1 555 999 1212',
                'company_address' => '',
            ])
            ->assertRedirect();

        app(PlatformSettingsService::class)->applyBranding();

        $this->assertNull(config('marketing.contact.address'));

        auth()->logout();

        $html = $this->get(route('marketing.contact'))
            ->assertOk()
            ->assertSee('hello@northline.example', false)
            ->assertSee('+1 555 999 1212', false)
            ->assertDontSee('1200 Market Street', false)
            ->assertDontSee('San Francisco', false)
            ->assertDontSee('PostalAddress', false)
            ->assertDontSee('streetAddress', false)
            ->assertDontSee('Google Maps', false)
            ->getContent();

        $this->assertStringNotContainsString('font-semibold text-slate-900">Address</', $html);
        $this->assertStringNotContainsString('>Address</', $html);
    }
}
