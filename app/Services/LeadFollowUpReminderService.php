<?php

namespace App\Services;

use App\Models\Lead;
use App\Notifications\LeadFollowUpDue;

class LeadFollowUpReminderService
{
    public function sendDueReminders(): int
    {
        if (! config('lead_reminders.enabled')) {
            return 0;
        }

        $sent = 0;

        Lead::query()
            ->dueForFollowUpReminder()
            ->with('assignee')
            ->chunkById(50, function ($leads) use (&$sent) {
                foreach ($leads as $lead) {
                    if (! $lead->assignee) {
                        continue;
                    }

                    $lead->assignee->notify(new LeadFollowUpDue($lead));

                    $lead->forceFill(['follow_up_reminder_sent_at' => now()])->save();

                    $sent++;
                }
            });

        return $sent;
    }
}
