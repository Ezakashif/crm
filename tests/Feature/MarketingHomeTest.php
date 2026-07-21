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
            ->assertSee(config('marketing.home.headline'), false)
            ->assertSee('Start free trial', false)
            ->assertSee('Book demo', false)
            ->assertSee('Trusted by growing revenue teams', false)
            ->assertSee('Everything your pipeline needs', false)
            ->assertSee('Up and running in four steps', false)
            ->assertSee('Why Algos', false)
            ->assertSee('Teams that switched to Algos', false)
            ->assertSee('Plans that scale with your team', false)
            ->assertSee('Questions, answered', false)
            ->assertSee('Ready to run your pipeline in Algos?', false);
    }

    public function test_home_includes_dashboard_preview_and_pricing_plans(): void
    {
        $this->get(route('marketing.home'))
            ->assertOk()
            ->assertSee('Algos CRM dashboard preview', false)
            ->assertSee('Starter', false)
            ->assertSee('Professional', false)
            ->assertSee('Enterprise', false)
            ->assertSee('data-mk-counter', false)
            ->assertSee('data-mk-scroll-top', false)
            ->assertSee('mk-hero-shape', false)
            ->assertSee('mk-stats-band', false);
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
