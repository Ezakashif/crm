<?php

namespace Database\Seeders;

use App\Models\Customer;
use App\Models\User;
use Carbon\Carbon;
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
        $this->call(RbacSeeder::class);

        User::factory()->superAdmin()->create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
        ]);

        $admin = User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        User::factory()->create([
            'name' => 'Sales Rep',
            'email' => 'sales@example.com',
        ]);

        // Spread customers across recent months so reports charts stay realistic.
        foreach (range(0, 19) as $index) {
            $createdAt = Carbon::now()
                ->subMonths(fake()->numberBetween(0, 5))
                ->subDays(fake()->numberBetween(0, 25))
                ->setTime(fake()->numberBetween(8, 18), fake()->numberBetween(0, 59));

            Customer::factory()
                ->active()
                ->create([
                    'created_by' => $admin->id,
                    'created_at' => $createdAt,
                    'updated_at' => $createdAt,
                ]);
        }

        $this->call(LeadSeeder::class);
        $this->call(TaskSeeder::class);
    }
}
