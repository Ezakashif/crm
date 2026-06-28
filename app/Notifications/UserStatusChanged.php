<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class UserStatusChanged extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public string $oldStatus,
        public string $newStatus,
        public User $changedBy,
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $message = (new MailMessage)
            ->subject('Your account status has been updated')
            ->greeting('Hello '.$notifiable->name.'!')
            ->line(sprintf(
                'Your account status was changed from %s to %s.',
                ucfirst($this->oldStatus),
                ucfirst($this->newStatus)
            ))
            ->line('Changed by: '.$this->changedBy->name);

        if ($this->newStatus !== 'active') {
            $message->line('You may not be able to log in until your account is active again.');
        }

        return $message->line('If you have questions, please contact your administrator.');
    }
}
