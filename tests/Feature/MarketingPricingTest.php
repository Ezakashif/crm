<?php

namespace Tests\Feature;

use Tests\TestCase;

class MarketingPricingTest extends TestCase
{
    public function test_pricing_page_shows_plans_and_toggle_copy(): void
    {
        $this->get(route('marketing.pricing'))
            ->assertOk()
            ->assertSee(config('marketing.pricing.headline'))
            ->assertSee('Starter')
            ->assertSee('Professional')
            ->assertSee('Enterprise')
            ->assertSee('Monthly')
            ->assertSee('Annual')
            ->assertSee(config('marketing.pricing.annual_discount_label'))
            ->assertSee('Start Free Trial')
            ->assertSee('Contact Sales');
    }

    public function test_pricing_page_includes_comparison_and_faq(): void
    {
        $this->get(route('marketing.pricing'))
            ->assertOk()
            ->assertSee('Feature comparison')
            ->assertSee('Lead management')
            ->assertSee('Multi-tenant architecture')
            ->assertSee('Pricing questions')
            ->assertSee('Do I need a credit card to start?')
            ->assertSee(config('marketing.pricing.future_note'))
            ->assertSee('No credit card required', false)
            ->assertSee('Recommended', false);
    }
}
