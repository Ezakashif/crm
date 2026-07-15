<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MarketingFoundationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_view_marketing_foundation_home(): void
    {
        $this->get(route('marketing.home'))
            ->assertOk()
            ->assertSee('Phase 3B', false)
            ->assertSee('Start free trial', false)
            ->assertSee('Book demo', false);
    }

    public function test_marketing_nav_routes_resolve(): void
    {
        $this->get(route('marketing.features'))->assertOk()->assertSee('Features', false);
        $this->get(route('marketing.pricing'))->assertOk()->assertSee('Pricing', false);
        $this->get(route('marketing.about'))->assertOk()->assertSee('About', false);
        $this->get(route('marketing.contact'))->assertOk()->assertSee('Contact', false);
    }

    public function test_authenticated_user_is_redirected_from_marketing_home_to_dashboard(): void
    {
        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('marketing.home'))
            ->assertRedirect(route('dashboard'));
    }
}
