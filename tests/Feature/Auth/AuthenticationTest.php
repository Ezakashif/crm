<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Session\TokenMismatchException;
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
    }

    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
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
