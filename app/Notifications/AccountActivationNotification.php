<?php

namespace App\Notifications;

use App\Notifications\Concerns\RendersTemplatedMail;
use App\Support\EmailVerification;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use App\Mail\TemplatedMail;

class AccountActivationNotification extends BaseVerifyEmail implements ShouldQueue
{
    use Queueable;
    use RendersTemplatedMail;

    /**
     * @return MailMessage|TemplatedMail
     */
    public function toMail(mixed $notifiable): MailMessage|TemplatedMail
    {
        $url = EmailVerification::signedUrl($notifiable);

        return $this->templatedMail($notifiable, 'account_activation', [
            'user_name' => $notifiable->name,
            'user_email' => $notifiable->getEmailForVerification(),
            'activation_url' => $url,
            'expires_minutes' => (string) config('auth.verification.expire', 60),
        ]);
    }
}
