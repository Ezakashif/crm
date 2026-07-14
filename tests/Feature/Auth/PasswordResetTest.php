<?php

namespace Tests\Feature\Auth;

use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
        $response->assertSee('name="company"', false);
    }

    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $slug = Company::query()->findOrFail($user->company_id)->slug;

        $this->post('/forgot-password', [
            'company' => $slug,
            'email' => $user->email,
        ]);

        Notification::assertSentTo($user, ResetPassword::class);
    }

    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $slug = Company::query()->findOrFail($user->company_id)->slug;

        $this->post('/forgot-password', [
            'company' => $slug,
            'email' => $user->email,
        ]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) {
            $response = $this->get('/reset-password/'.$notification->token);

            $response->assertStatus(200);

            return true;
        });
    }

    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();
        $slug = Company::query()->findOrFail($user->company_id)->slug;

        $this->post('/forgot-password', [
            'company' => $slug,
            'email' => $user->email,
        ]);

        Notification::assertSentTo($user, ResetPassword::class, function ($notification) use ($user, $slug) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'company' => $slug,
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            return true;
        });
    }
}
