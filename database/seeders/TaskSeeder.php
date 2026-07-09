<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Seed sample tasks for existing users without wiping other data.
     */
    public function run(): void
    {
        $admin = User::query()->where('email', 'admin@example.com')->first()
            ?? User::query()->whereHas('roles', fn ($q) => $q->where('slug', 'admin'))->first()
            ?? User::factory()->admin()->create([
                'name' => 'Admin User',
                'email' => 'admin@example.com',
            ]);

        $sales = User::query()->where('email', 'sales@example.com')->first()
            ?? User::query()->whereHas('roles', fn ($q) => $q->where('slug', 'sales'))->first()
            ?? User::factory()->create([
                'name' => 'Sales Rep',
                'email' => 'sales@example.com',
            ]);

        $assignees = collect([$admin, $sales])->filter()->unique('id')->values();
        $customers = Customer::query()->get();
        $leads = Lead::query()->get();

        $samples = [
            ['title' => 'Call new website lead', 'status' => 'pending', 'priority' => 'high', 'due' => now()->subDays(2)],
            ['title' => 'Send proposal follow-up', 'status' => 'pending', 'priority' => 'urgent', 'due' => now()->subDay()],
            ['title' => 'Prepare demo agenda', 'status' => 'pending', 'priority' => 'medium', 'due' => now()->addDays(1)],
            ['title' => 'Update CRM notes', 'status' => 'pending', 'priority' => 'low', 'due' => now()->addDays(3)],
            ['title' => 'Qualify inbound lead', 'status' => 'in_progress', 'priority' => 'high', 'due' => now()->addDays(2)],
            ['title' => 'Schedule discovery call', 'status' => 'in_progress', 'priority' => 'medium', 'due' => today()],
            ['title' => 'Review contract draft', 'status' => 'in_progress', 'priority' => 'urgent', 'due' => now()->addDay()],
            ['title' => 'Close won opportunity checklist', 'status' => 'completed', 'priority' => 'medium', 'due' => now()->subDays(3)],
            ['title' => 'Send onboarding email', 'status' => 'completed', 'priority' => 'low', 'due' => now()->subDays(5)],
            ['title' => 'Archive stale opportunity', 'status' => 'cancelled', 'priority' => 'low', 'due' => now()->subDays(7)],
            ['title' => 'Reconnect with lost lead', 'status' => 'cancelled', 'priority' => 'medium', 'due' => now()->subDays(4)],
            ['title' => 'Confirm meeting with customer', 'status' => 'pending', 'priority' => 'high', 'due' => now()->addDays(4)],
        ];

        foreach ($samples as $index => $sample) {
            $assignee = $assignees[$index % $assignees->count()];

            Task::factory()
                ->status($sample['status'], $index)
                ->assignedTo($assignee)
                ->create([
                    'created_by' => $admin->id,
                    'title' => $sample['title'],
                    'priority' => $sample['priority'],
                    'due_date' => $sample['due'],
                    'customer_id' => $customers->isNotEmpty() && fake()->boolean(40)
                        ? $customers->random()->id
                        : null,
                    'lead_id' => $leads->isNotEmpty() && fake()->boolean(40)
                        ? $leads->random()->id
                        : null,
                    'description' => fake()->sentence(12),
                ]);
        }

        $this->command?->info('Seeded '.count($samples).' sample tasks.');
    }
}
