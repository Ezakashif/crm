<?php

namespace App\Console\Commands;

use App\Services\LeadFollowUpReminderService;
use Illuminate\Console\Command;

class SendLeadFollowUpReminders extends Command
{
    protected $signature = 'leads:send-follow-up-reminders';

    protected $description = 'Notify assigned users about leads with due follow-up dates';

    public function handle(LeadFollowUpReminderService $service): int
    {
        $sent = $service->sendDueReminders();

        $this->info("Sent {$sent} follow-up reminder(s).");

        return self::SUCCESS;
    }
}
