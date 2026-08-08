<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plan;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $plans = [
            [
                'name' => 'Free',
                'slug' => 'free',
                'short_description' => 'Essential CRM tools for small teams getting started.',
                'description' => 'A simple CRM plan for managing leads, customers, tasks, and basic sales activities.',
                'max_users' => 2,
                'max_leads' => 100,
                'max_customers' => 100,

                'price_cents' => 0,
                'monthly_price' => 0.00,
                'yearly_price' => 0.00,
                'currency' => 'USD',
                'billing_cycle' => 'monthly',
                'trial_days' => 0,

                'is_free' => true,
                'is_featured' => false,
                'is_public' => true,
                'sort_order' => 1,

                'notes' => 'Free plan for small teams.',
                'created_by' => null,
                'updated_by' => null,

                'is_default' => true,
                'is_active' => true,
            ],

            [
                'name' => 'Growth',
                'slug' => 'growth',
                'short_description' => 'Powerful CRM features for growing businesses.',
                'description' => 'A complete CRM solution for growing businesses that need more users, leads, customers, reporting, analytics, and team management.',
                'max_users' => 10,
                'max_leads' => 1000,
                'max_customers' => 1000,

                'price_cents' => 4900,
                'monthly_price' => 49.00,
                'yearly_price' => 490.00,
                'currency' => 'USD',
                'billing_cycle' => 'monthly',
                'trial_days' => 30,

                'is_free' => false,
                'is_featured' => true,
                'is_public' => true,
                'sort_order' => 2,

                'notes' => 'Recommended plan for growing businesses.',
                'created_by' => null,
                'updated_by' => null,

                'is_default' => false,
                'is_active' => true,
            ],

            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'short_description' => 'Advanced CRM capabilities for larger organizations.',
                'description' => 'An advanced CRM plan for organizations requiring higher limits, advanced administration, analytics, and premium capabilities.',
                'max_users' => 50,
                'max_leads' => 10000,
                'max_customers' => 10000,

                'price_cents' => 14900,
                'monthly_price' => 149.00,
                'yearly_price' => 1490.00,
                'currency' => 'USD',
                'billing_cycle' => 'monthly',
                'trial_days' => 30,

                'is_free' => false,
                'is_featured' => false,
                'is_public' => true,
                'sort_order' => 3,

                'notes' => 'Enterprise plan for larger organizations.',
                'created_by' => null,
                'updated_by' => null,

                'is_default' => false,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}