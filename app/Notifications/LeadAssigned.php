<?php

namespace App\Notifications;

use App\Models\Lead;
use Illuminate\Notifications\Notification;

class LeadAssigned extends Notification
{
    public function __construct(public Lead $lead) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'subject' => 'Lead assigned to you',
            'message' => 'You have been assigned the lead '.$this->lead->name.'.',
            'url' => route('leads.show', $this->lead, false),
            'lead_id' => $this->lead->id,
            'lead_name' => $this->lead->name,
        ];
    }
}
