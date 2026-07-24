<?php

namespace App\Notifications;

use App\Mail\TemplatedMail;
use App\Notifications\Concerns\RendersTemplatedMail;
use Illuminate\Auth\Notifications\ResetPassword as BaseResetPassword;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PasswordResetNotification extends BaseResetPassword implements ShouldQueue
{
    use Queueable;
    use RendersTemplatedMail;

    /**
     * @return MailMessage|TemplatedMail
     */
    public function toMail(mixed $notifiable): MailMessage|TemplatedMail
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return $this->templatedMail($notifiable, 'password_reset', [
            'user_name' => $notifiable->name,
            'user_email' => $notifiable->getEmailForPasswordReset(),
            'reset_url' => $url,
            'expires_minutes' => (string) config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60),
        ]);
    }
}
