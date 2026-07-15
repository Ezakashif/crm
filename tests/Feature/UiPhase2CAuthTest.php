<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\SuperAdmin\PlatformSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UiPhase2CAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_uses_polished_auth_patterns(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('crm-auth-card', false)
            ->assertSee('crm-auth-label', false)
            ->assertSee('crm-auth-lead', false)
            ->assertSee('Workspace slug')
            ->assertSee('Sign in to your workspace')
            ->assertSee('css/crm-app.css', false);
    }

    public function test_forgot_password_screen_uses_labels_and_footer(): void
    {
        $this->get(route('password.request'))
            ->assertOk()
            ->assertSee('crm-auth-label', false)
            ->assertSee('Back to login')
            ->assertSee('Forgot your password?');
    }

    public function test_register_screen_uses_polished_patterns_when_enabled(): void
    {
        app(PlatformSettingsService::class)->setMany(['registration_enabled' => true]);

        $this->get(route('register'))
            ->assertOk()
            ->assertSee('Create your workspace')
            ->assertSee('crm-auth-label', false)
            ->assertSee('Company name')
            ->assertSee('I already have an account');
    }

    public function test_confirm_password_screen_uses_lockscreen_polish(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('password.confirm'))
            ->assertOk()
            ->assertSee('crm-auth-lockscreen', false)
            ->assertSee('Confirm your password to continue.');
    }
}
