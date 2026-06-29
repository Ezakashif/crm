<?php

namespace Tests\Feature;

use App\Models\Lead;
use App\Models\User;
use App\Notifications\LeadFollowUpDue;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class LeadFollowUpReminderTest extends TestCase
{
    use RefreshDatabase;

    public function test_command_sends_notification_to_assigned_user(): void
    {
        Notification::fake();

        $user = User::factory()->create(['status' => 'active']);
        $lead = Lead::factory()->assignedTo($user)->create([
            'follow_up_date' => today(),
            'status' => 'new',
            'follow_up_reminder_sent_at' => null,
        ]);

        $this->artisan('leads:send-follow-up-reminders')->assertSuccessful();

        Notification::assertSentTo($user, LeadFollowUpDue::class, function (LeadFollowUpDue $notification) use ($lead) {
            return $notification->lead->is($lead);
        });

        $this->assertNotNull($lead->fresh()->follow_up_reminder_sent_at);
    }

    public function test_command_does_not_send_duplicate_reminders(): void
    {
        Notification::fake();

        $user = User::factory()->create(['status' => 'active']);
        Lead::factory()->assignedTo($user)->create([
            'follow_up_date' => today(),
            'follow_up_reminder_sent_at' => now(),
        ]);

        $this->artisan('leads:send-follow-up-reminders')->assertSuccessful();

        Notification::assertNothingSent();
    }

    public function test_command_skips_won_leads(): void
    {
        Notification::fake();

        $user = User::factory()->create(['status' => 'active']);
        Lead::factory()->assignedTo($user)->create([
            'follow_up_date' => today(),
            'status' => 'won',
            'follow_up_reminder_sent_at' => null,
        ]);

        $this->artisan('leads:send-follow-up-reminders')->assertSuccessful();

        Notification::assertNothingSent();
    }

    public function test_changing_follow_up_date_resets_reminder_flag(): void
    {
        $lead = Lead::factory()->create([
            'follow_up_date' => today(),
            'follow_up_reminder_sent_at' => now(),
        ]);

        $lead->update(['follow_up_date' => today()->addDays(5)]);

        $this->assertNull($lead->fresh()->follow_up_reminder_sent_at);
    }

    public function test_user_can_view_notifications_page(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('notifications.index'))
            ->assertOk()
            ->assertSee('Notifications');
    }
}
