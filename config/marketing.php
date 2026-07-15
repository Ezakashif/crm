<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Brand
    |--------------------------------------------------------------------------
    */

    'name' => env('MARKETING_BRAND_NAME', 'Algos'),
    'tagline' => env('MARKETING_TAGLINE', 'CRM that helps growing teams close more deals'),
    'description' => env(
        'MARKETING_DESCRIPTION',
        'Algos is a modern multi-tenant CRM for lead management, customer success, tasks, and team collaboration.'
    ),

    /*
    |--------------------------------------------------------------------------
    | Contact & company
    |--------------------------------------------------------------------------
    */

    'contact' => [
        'email' => env('MARKETING_CONTACT_EMAIL', 'hello@algos.test'),
        'phone' => env('MARKETING_CONTACT_PHONE', '+1 (555) 010-2000'),
        'address' => env('MARKETING_CONTACT_ADDRESS', '1200 Market Street, Suite 400, San Francisco, CA 94103'),
    ],

    'social' => [
        'twitter' => env('MARKETING_SOCIAL_TWITTER', '#'),
        'linkedin' => env('MARKETING_SOCIAL_LINKEDIN', '#'),
        'github' => env('MARKETING_SOCIAL_GITHUB', '#'),
    ],

    /*
    |--------------------------------------------------------------------------
    | CTAs
    |--------------------------------------------------------------------------
    */

    'cta' => [
        'trial_route' => 'register',
        'demo_route' => 'marketing.contact',
        'demo_query' => ['intent' => 'demo'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pricing placeholders (Phase 3E will consume these)
    |--------------------------------------------------------------------------
    */

    'pricing' => [
        'currency' => 'USD',
        'annual_discount_label' => 'Save 20%',
        'plans' => [
            [
                'id' => 'starter',
                'name' => 'Starter',
                'description' => 'For small teams getting organized.',
                'monthly' => 29,
                'annual' => 23,
                'highlighted' => false,
                'cta' => 'Start Free Trial',
                'features' => [
                    'Up to 5 users',
                    'Lead & customer management',
                    'Task boards',
                    'Email support',
                ],
            ],
            [
                'id' => 'professional',
                'name' => 'Professional',
                'description' => 'For growing sales teams.',
                'monthly' => 79,
                'annual' => 63,
                'highlighted' => true,
                'cta' => 'Start Free Trial',
                'features' => [
                    'Up to 25 users',
                    'Kanban pipelines',
                    'Reports & analytics',
                    'CSV import / export',
                    'Priority support',
                ],
            ],
            [
                'id' => 'enterprise',
                'name' => 'Enterprise',
                'description' => 'For multi-team organizations.',
                'monthly' => 149,
                'annual' => 119,
                'highlighted' => false,
                'cta' => 'Contact Sales',
                'features' => [
                    'Unlimited users',
                    'Role & permission management',
                    'Activity logs',
                    'Dedicated onboarding',
                    'SLA & SSO (coming soon)',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation
    |--------------------------------------------------------------------------
    */

    'nav' => [
        ['label' => 'Features', 'route' => 'marketing.features'],
        ['label' => 'Pricing', 'route' => 'marketing.pricing'],
        ['label' => 'About', 'route' => 'marketing.about'],
        ['label' => 'Contact', 'route' => 'marketing.contact'],
    ],

];
