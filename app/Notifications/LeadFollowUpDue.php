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

    public function __construct(
        public Lead $lead,
        public string $tier = 'due',
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        [$subject, $line] = $this->copy();

        $message = (new MailMessage)
            ->subject($subject.': '.$this->lead->name)
            ->greeting('Hello '.$notifiable->name.'!')
            ->line($line)
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
        [$subject, $line] = $this->copy();

        return [
            'lead_id' => $this->lead->id,
            'lead_name' => $this->lead->name,
            'follow_up_date' => $this->lead->follow_up_date?->toDateString(),
            'tier' => $this->tier,
            'is_overdue' => $this->isOverdue(),
            'message' => $line,
            'url' => route('leads.show', $this->lead),
            'subject' => $subject,
        ];
    }

    /**
     * @return array{0: string, 1: string}
     */
    protected function copy(): array
    {
        $dateLabel = $this->lead->follow_up_date?->format('M j, Y') ?? 'unknown date';

        return match ($this->tier) {
            'day_before' => [
                'Follow-up tomorrow',
                'Reminder: follow-up for '.$this->lead->name.' is scheduled for tomorrow ('.$dateLabel.').',
            ],
            'hours_before' => [
                'Follow-up in 2 hours',
                'Reminder: follow-up for '.$this->lead->name.' is coming up in about 2 hours ('.$dateLabel.').',
            ],
            default => $this->isOverdue()
                ? [
                    'Follow-up overdue',
                    'You have a lead follow-up that was due on '.$dateLabel.'.',
                ]
                : [
                    'Follow-up due today',
                    'You have a lead follow-up that is due today ('.$dateLabel.').',
                ],
        };
    }

    protected function isOverdue(): bool
    {
        if (! $this->lead->follow_up_date) {
            return false;
        }

        return $this->lead->follow_up_date->isPast() && ! $this->lead->follow_up_date->isToday();
    }
}
