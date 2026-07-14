<?php

namespace Tests\Feature\Auth;

use App\Models\Company;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
        $response->assertSee('name="company"', false);
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();
        $slug = Company::query()->findOrFail($user->company_id)->slug;

        $response = $this->post('/login', [
            'company' => $slug,
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_tenant_login_without_company_slug_fails(): void
    {
        $user = User::factory()->create([
            'email' => 'tenant@example.com',
        ]);

        $this->post('/login', [
            'email' => 'tenant@example.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_same_email_across_companies_authenticates_correct_tenant(): void
    {
        $companyA = Company::factory()->create(['slug' => 'acme']);
        $companyB = Company::factory()->create(['slug' => 'beta']);

        $userA = User::factory()->create([
            'company_id' => $companyA->id,
            'email' => 'shared@example.com',
            'password' => Hash::make('password-a'),
        ]);

        $userB = User::factory()->create([
            'company_id' => $companyB->id,
            'email' => 'shared@example.com',
            'password' => Hash::make('password-b'),
        ]);

        $this->post('/login', [
            'company' => 'beta',
            'email' => 'shared@example.com',
            'password' => 'password-b',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($userB);

        $this->post('/logout');
        $this->assertGuest();

        $this->post('/login', [
            'company' => 'acme',
            'email' => 'shared@example.com',
            'password' => 'password-a',
        ])->assertRedirect(route('dashboard', absolute: false));

        $this->assertAuthenticatedAs($userA);
    }

    public function test_wrong_company_slug_does_not_authenticate_even_with_valid_password(): void
    {
        $companyA = Company::factory()->create(['slug' => 'acme']);
        Company::factory()->create(['slug' => 'beta']);

        User::factory()->create([
            'company_id' => $companyA->id,
            'email' => 'shared@example.com',
        ]);

        $this->post('/login', [
            'company' => 'beta',
            'email' => 'shared@example.com',
            'password' => 'password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();
        $slug = Company::query()->findOrFail($user->company_id)->slug;

        $this->post('/login', [
            'company' => $slug,
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
    }

    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }

    public function test_logout_with_expired_session_redirects_to_login(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        // Simulate an expired / invalid CSRF token from a stale page.
        $response = $this->post('/logout', ['_token' => 'stale-token']);

        $this->assertGuest();
        $response->assertRedirect(route('login'));
    }

    public function test_token_mismatch_redirects_to_login(): void
    {
        // CSRF is skipped during PHPUnit; exercise the 419 / session-expired handler.
        Route::get('/__test/csrf-expired', function () {
            throw new TokenMismatchException('CSRF token mismatch.');
        })->middleware('web');

        $response = $this->get('/__test/csrf-expired');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');
    }

    public function test_http_419_redirects_to_login(): void
    {
        Route::get('/__test/page-expired', function () {
            throw new HttpException(419, 'Page Expired');
        })->middleware('web');

        $response = $this->get('/__test/page-expired');

        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');
    }
}
