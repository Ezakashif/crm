<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingHomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_marketing_home(): void
    {
        $this->get(route('marketing.home'))
            ->assertOk()
            ->assertSee(config('marketing.name'), false)
            ->assertSee('hero', false)
            ->assertSee('marketing/assets/css/main.css', false)
            ->assertSee('Get Started', false)
            ->assertSee('Contact', false);
    }

    public function test_home_uses_instant_marketing_template_assets(): void
    {
        $this->get(route('marketing.home'))
            ->assertOk()
            ->assertSee('marketing/assets/vendor/bootstrap/css/bootstrap.min.css', false)
            ->assertSee('marketing/assets/js/main.js', false)
            ->assertSee('id="header"', false)
            ->assertSee('id="footer"', false)
            ->assertSee('scroll-top', false);
    }

    public function test_authenticated_user_is_redirected_from_home_to_dashboard(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('marketing.home'))
            ->assertRedirect(route('dashboard'));
    }
}
