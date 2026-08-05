<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\RbacSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DocsViewerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(RbacSeeder::class);
    }

    public function test_guest_cannot_view_docs(): void
    {
        $this->get(route('docs.index'))->assertRedirect(route('login'));
    }

    public function test_tenant_admin_can_view_docs_home_and_nested_page(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('docs.index'))
            ->assertOk()
            ->assertSee('CRM Documentation', false)
            ->assertSee('Contents', false);

        $this->actingAs($admin)
            ->get(route('docs.show', ['path' => 'getting-started/installation']))
            ->assertOk()
            ->assertSee('Installation', false)
            ->assertSee('composer install', false);
    }

    public function test_docs_rewrite_relative_markdown_links(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('docs.show', ['path' => 'getting-started/installation']))
            ->assertOk()
            ->assertSee(route('docs.show', ['path' => 'getting-started/configuration'], false), false);
    }

    public function test_path_traversal_is_rejected(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get('/docs/../.env')
            ->assertNotFound();

        $this->actingAs($admin)
            ->get('/docs/'.urlencode('../../composer.json'))
            ->assertNotFound();
    }

    public function test_unknown_doc_returns_not_found(): void
    {
        $admin = User::factory()->admin()->create();

        $this->actingAs($admin)
            ->get(route('docs.show', ['path' => 'does-not-exist']))
            ->assertNotFound();
    }

    public function test_super_admin_can_view_docs(): void
    {
        $superAdmin = User::factory()->superAdmin()->create();

        $this->actingAs($superAdmin)
            ->get(route('docs.index'))
            ->assertOk()
            ->assertSee('Documentation', false);
    }
}
