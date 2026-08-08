<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

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
                'short_description' => 'Simple CRM for small teams getting started.',
                'description' => 'A free CRM plan with the essential tools needed to manage leads, customers, tasks, and sales activities.',
                'price_cents' => 0,
                'monthly_price' => 0,
                'yearly_price' => 0,
                'currency' => 'USD',
                'billing_cycle' => 'monthly',
                'trial_days' => 0,
                'is_free' => true,
                'is_featured' => false,
                'is_public' => true,
                'sort_order' => 1,
                'notes' => 'Free plan.',
            ],

              [
                'name' => 'Growth',
                'slug' => 'growth',
                'short_description' => 'Powerful CRM features for growing businesses.',
                'description' => 'Everything needed to manage a growing sales team, including advanced CRM features, reports, analytics, and team management.',
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
            ],

             [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'short_description' => 'Advanced CRM capabilities for larger organizations.',
                'description' => 'A comprehensive CRM plan for organizations that need advanced administration, scalability, integrations, and premium features.',
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
                'notes' => 'Enterprise plan.',
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
