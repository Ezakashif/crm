<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Customer;
use App\Models\User;
use App\Support\CurrentCompany;
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
        $this->call(EmailTemplateSeeder::class);

        // Production CompanyScope is fail-closed without tenant context.
        $company = Company::query()->firstOrCreate(
            ['slug' => Company::DEFAULT_SLUG],
            [
                'name' => 'Default Company',
                'status' => Company::STATUS_ACTIVE,
            ],
        );
        app(CurrentCompany::class)->set($company);

        User::withoutCompanyScope()->firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'password' => 'password',
                'email_verified_at' => now(),
                'role' => 'admin',
                'status' => 'active',
                'is_super_admin' => true,
                'company_id' => null,
            ],
        );

        $admin = User::withoutCompanyScope()->firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'password' => 'password',
                'email_verified_at' => now(),
                'role' => 'admin',
                'status' => 'active',
                'company_id' => $company->id,
            ],
        );
        $admin->syncRolesFromLegacyColumn();

        $sales = User::withoutCompanyScope()->firstOrCreate(
            ['email' => 'sales@example.com'],
            [
                'name' => 'Sales Rep',
                'password' => 'password',
                'email_verified_at' => now(),
                'role' => 'user',
                'status' => 'active',
                'company_id' => $company->id,
            ],
        );
        $sales->syncRolesFromLegacyColumn();

        // Spread customers across recent months so reports charts stay realistic.
        if (Customer::withoutCompanyScope()->doesntExist()) {
            foreach (range(0, 19) as $index) {
                $createdAt = Carbon::now()
                    ->subMonths(fake()->numberBetween(0, 5))
                    ->subDays(fake()->numberBetween(0, 25))
                    ->setTime(fake()->numberBetween(8, 18), fake()->numberBetween(0, 59));

                Customer::factory()
                    ->active()
                    ->create([
                        'company_id' => $company->id,
                        'created_by' => $admin->id,
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);
            }
        }

        $this->call(LeadSeeder::class);
        $this->call(TaskSeeder::class);
    }
}
