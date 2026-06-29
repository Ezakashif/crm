<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class LeadFollowUpDue extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Lead $lead) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $isOverdue = $this->lead->follow_up_date->isPast() && ! $this->lead->follow_up_date->isToday();
        $whenLabel = $isOverdue
            ? 'was due on '.$this->lead->follow_up_date->format('M j, Y')
            : 'is due today ('.$this->lead->follow_up_date->format('M j, Y').')';

        $message = (new MailMessage)
            ->subject('Follow-up '.$whenLabel.': '.$this->lead->name)
            ->greeting('Hello '.$notifiable->name.'!')
            ->line('You have a lead follow-up that '.$whenLabel.'.')
            ->line('Lead: '.$this->lead->name);

        if (filled($this->lead->company)) {
            $message->line('Company: '.$this->lead->company);
        }

        if (filled($this->lead->phone)) {
            $message->line('Phone: '.$this->lead->phone);
        }

        if (filled($this->lead->email)) {
            $message->line('Email: '.$this->lead->email);
        }

        return $message
            ->action('View Lead', route('leads.show', $this->lead))
            ->line('Log your contact in the CRM after reaching out.');
    }

    public function toArray(object $notifiable): array
    {
        $isOverdue = $this->lead->follow_up_date->isPast() && ! $this->lead->follow_up_date->isToday();

        return [
            'lead_id' => $this->lead->id,
            'lead_name' => $this->lead->name,
            'follow_up_date' => $this->lead->follow_up_date->toDateString(),
            'is_overdue' => $isOverdue,
            'message' => $isOverdue
                ? 'Follow-up overdue for '.$this->lead->name
                : 'Follow-up due today for '.$this->lead->name,
            'url' => route('leads.show', $this->lead),
        ];
    }
}
