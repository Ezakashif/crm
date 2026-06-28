<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\Lead;
use App\Models\Task;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        $salesRep = User::factory()->create([
            'name' => 'Sales Rep',
            'email' => 'sales@example.com',
        ]);

        $users = collect([$admin, $salesRep]);

        Customer::factory(15)
            ->active()
            ->create(['created_by' => $admin->id]);

        $leadStatuses = ['new', 'contacted', 'qualified', 'proposal_sent', 'won', 'lost'];

        foreach ($leadStatuses as $status) {
            $count = fake()->numberBetween(2, 5);

            for ($i = 0; $i < $count; $i++) {
                Lead::factory()
                    ->status($status, $i)
                    ->assignedTo($users->random())
                    ->create(['created_by' => $admin->id]);
            }
        }

        $customers = Customer::all();
        $leads = Lead::all();

        $taskStatuses = ['pending', 'in_progress', 'completed', 'cancelled'];

        foreach ($taskStatuses as $status) {
            $count = fake()->numberBetween(2, 5);

            for ($i = 0; $i < $count; $i++) {
                Task::factory()
                    ->status($status, $i)
                    ->assignedTo($users->random())
                    ->create([
                        'created_by' => $admin->id,
                        'customer_id' => fake()->optional(0.5)->passthrough($customers->random()->id),
                        'lead_id' => fake()->optional(0.3)->passthrough($leads->random()->id),
                    ]);
            }
        }
    }
}
