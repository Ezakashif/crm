<?php

namespace Tests\Feature;

use Tests\TestCase;

class MarketingDocumentationTest extends TestCase
{
    public function test_guest_can_view_documentation_page(): void
    {
        $this->get(route('marketing.documentation'))
            ->assertOk()
            ->assertSee(config('marketing.documentation.headline'), false)
            ->assertSee(config('marketing.documentation.subheadline'), false)
            ->assertSee('Getting started', false)
            ->assertSee('Full product docs (signed in)', false)
            ->assertSee(route('login'), false);
    }

    public function test_documentation_links_appear_on_home_pricing_and_footer(): void
    {
        $docsUrl = route('marketing.documentation');

        $this->get(route('marketing.home'))
            ->assertOk()
            ->assertSee($docsUrl, false);

        $this->get(route('marketing.pricing'))
            ->assertOk()
            ->assertSee($docsUrl, false);

        $this->get(route('marketing.about'))
            ->assertOk()
            ->assertSee($docsUrl, false);
    }
}
