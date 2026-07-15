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

    /*
    |--------------------------------------------------------------------------
    | Home page content
    |--------------------------------------------------------------------------
    */

    'home' => [
        'headline' => 'Close more deals with a CRM your team will actually use',
        'subheadline' => 'Algos brings leads, customers, tasks, and reporting together in one calm workspace built for growing teams.',
        'trusted_by' => [
            'Northline',
            'Bright Harbor',
            'Cascade Labs',
            'Summit Peak',
            'Harbor & Co',
            'Vertex Works',
        ],
        'features' => [
            [
                'icon' => 'user-plus',
                'title' => 'Lead management',
                'description' => 'Capture every inquiry, score interest, and move deals through a clear pipeline.',
            ],
            [
                'icon' => 'users',
                'title' => 'Customer management',
                'description' => 'Keep ownership, history, and context in one place after the deal is won.',
            ],
            [
                'icon' => 'kanban',
                'title' => 'Kanban boards',
                'description' => 'Visual boards for leads and tasks so progress stays obvious to the whole team.',
            ],
            [
                'icon' => 'bar-chart',
                'title' => 'Reports & analytics',
                'description' => 'See conversion, workload, and pipeline health without exporting to spreadsheets.',
            ],
            [
                'icon' => 'shield',
                'title' => 'Roles & permissions',
                'description' => 'Give every teammate the right access with company-scoped roles.',
            ],
            [
                'icon' => 'layers',
                'title' => 'Multi-tenant ready',
                'description' => 'Isolated company data and settings on a shared, scalable platform.',
            ],
        ],
        'how_it_works' => [
            [
                'step' => '01',
                'title' => 'Create your workspace',
                'description' => 'Sign up, invite your team, and set roles in minutes.',
            ],
            [
                'step' => '02',
                'title' => 'Import your pipeline',
                'description' => 'Bring leads and customers in with CSV, or start fresh.',
            ],
            [
                'step' => '03',
                'title' => 'Run every follow-up',
                'description' => 'Track tasks, activities, and next steps from one dashboard.',
            ],
            [
                'step' => '04',
                'title' => 'Measure what converts',
                'description' => 'Use reports to double down on the work that wins deals.',
            ],
        ],
        'why_us' => [
            [
                'icon' => 'zap',
                'title' => 'Fast to adopt',
                'description' => 'A clean UI that sales and ops teams understand on day one.',
            ],
            [
                'icon' => 'shield',
                'title' => 'Built for control',
                'description' => 'Permissions, activity logs, and tenant isolation from the start.',
            ],
            [
                'icon' => 'layout-dashboard',
                'title' => 'Clarity over clutter',
                'description' => 'Dashboards and boards that surface what matters—nothing more.',
            ],
        ],
        'stats' => [
            ['value' => '3x', 'label' => 'Faster follow-ups'],
            ['value' => '98%', 'label' => 'Pipeline visibility'],
            ['value' => '14+', 'label' => 'CRM modules'],
            ['value' => '24/7', 'label' => 'Secure access'],
        ],
        'testimonials' => [
            [
                'quote' => 'We finally have one place for leads, customers, and follow-ups. The team adopted it in a week.',
                'name' => 'Maya Chen',
                'role' => 'Head of Sales',
                'company' => 'Northline',
            ],
            [
                'quote' => 'Kanban boards and role permissions gave us structure without slowing the reps down.',
                'name' => 'Jordan Blake',
                'role' => 'COO',
                'company' => 'Bright Harbor',
            ],
            [
                'quote' => 'Importing our CSV data was painless. Reporting helped us see what was actually converting.',
                'name' => 'Priya Nair',
                'role' => 'Revenue Ops',
                'company' => 'Cascade Labs',
            ],
        ],
        'faqs' => [
            [
                'id' => 'trial',
                'question' => 'Is there a free trial?',
                'answer' => 'Yes. Start a free trial to explore core CRM modules before choosing a plan. No credit card required to begin.',
            ],
            [
                'id' => 'import',
                'question' => 'Can we import existing data?',
                'answer' => 'CSV import and export are available for leads, customers, and users so you can migrate without starting from scratch.',
            ],
            [
                'id' => 'tenants',
                'question' => 'Is Algos multi-tenant?',
                'answer' => 'Yes. Each company gets isolated data, roles, and settings inside a shared platform architecture.',
            ],
            [
                'id' => 'permissions',
                'question' => 'Can we control who sees what?',
                'answer' => 'Role and permission management lets admins grant precise access across leads, customers, tasks, reports, and more.',
            ],
            [
                'id' => 'support',
                'question' => 'What support is included?',
                'answer' => 'Starter includes email support. Professional and Enterprise plans include faster response paths and onboarding options.',
            ],
        ],
    ],

];
