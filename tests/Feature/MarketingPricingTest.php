<?php

namespace Tests\Feature;

use Tests\TestCase;

class MarketingPricingTest extends TestCase
{
    public function test_pricing_page_shows_plans_and_billing_copy(): void
    {
        $this->get(route('marketing.pricing'))
            ->assertOk()
            ->assertSee(config('marketing.pricing.headline'))
            ->assertSee('Starter')
            ->assertSee('Professional')
            ->assertSee('Enterprise')
            ->assertSee(config('marketing.pricing.annual_discount_label'))
            ->assertSee('Start Free Trial')
            ->assertSee('Contact Sales')
            ->assertSee(config('marketing.pricing.future_note'));
    }

    public function test_pricing_page_includes_faq(): void
    {
        $this->get(route('marketing.pricing'))
            ->assertOk()
            ->assertSee('Pricing FAQ')
            ->assertSee('Are these final prices?');
    }
}
