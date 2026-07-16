<?php

namespace Tests\Feature;

use Tests\TestCase;

class MarketingRoutesTest extends TestCase
{
    public function test_marketing_nav_routes_resolve(): void
    {
        $this->get(route('marketing.features'))->assertOk()->assertSee('Every module your revenue team needs', false);
        $this->get(route('marketing.pricing'))->assertOk()->assertSee('Pricing', false);
        $this->get(route('marketing.about'))->assertOk()->assertSee('About', false);
        $this->get(route('marketing.contact'))->assertOk()->assertSee('Contact', false);
    }
}
