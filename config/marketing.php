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
        'headline' => 'Simple pricing for growing teams',
        'subheadline' => 'Start with a free trial. Upgrade when your pipeline and team need more power. Prices are placeholders until billing goes live.',
        'plans' => [
            [
                'id' => 'starter',
                'name' => 'Starter',
                'description' => 'For small teams getting organized.',
                'monthly' => 29,
                'annual' => 23,
                'highlighted' => false,
                'cta' => 'Start Free Trial',
                'cta_type' => 'trial',
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
                'cta_type' => 'trial',
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
                'cta_type' => 'demo',
                'features' => [
                    'Unlimited users',
                    'Role & permission management',
                    'Activity logs',
                    'Dedicated onboarding',
                    'SLA & SSO (coming soon)',
                ],
            ],
        ],
        'comparison' => [
            ['feature' => 'Users', 'starter' => 'Up to 5', 'professional' => 'Up to 25', 'enterprise' => 'Unlimited'],
            ['feature' => 'Lead management', 'starter' => true, 'professional' => true, 'enterprise' => true],
            ['feature' => 'Customer management', 'starter' => true, 'professional' => true, 'enterprise' => true],
            ['feature' => 'Task management', 'starter' => true, 'professional' => true, 'enterprise' => true],
            ['feature' => 'Kanban boards', 'starter' => 'Basic', 'professional' => true, 'enterprise' => true],
            ['feature' => 'Dashboard analytics', 'starter' => 'Core', 'professional' => true, 'enterprise' => true],
            ['feature' => 'Reports', 'starter' => false, 'professional' => true, 'enterprise' => true],
            ['feature' => 'CSV import / export', 'starter' => false, 'professional' => true, 'enterprise' => true],
            ['feature' => 'Global search', 'starter' => true, 'professional' => true, 'enterprise' => true],
            ['feature' => 'Notifications', 'starter' => true, 'professional' => true, 'enterprise' => true],
            ['feature' => 'Role & permissions', 'starter' => 'Basic', 'professional' => true, 'enterprise' => true],
            ['feature' => 'Activity logs', 'starter' => false, 'professional' => true, 'enterprise' => true],
            ['feature' => 'Company management', 'starter' => true, 'professional' => true, 'enterprise' => true],
            ['feature' => 'Multi-tenant architecture', 'starter' => true, 'professional' => true, 'enterprise' => true],
            ['feature' => 'Super Admin tools', 'starter' => false, 'professional' => false, 'enterprise' => true],
            ['feature' => 'Dedicated onboarding', 'starter' => false, 'professional' => false, 'enterprise' => true],
            ['feature' => 'SSO / SLA (coming soon)', 'starter' => false, 'professional' => false, 'enterprise' => 'Roadmap'],
            ['feature' => 'Support', 'starter' => 'Email', 'professional' => 'Priority', 'enterprise' => 'Dedicated'],
        ],
        'faqs' => [
            [
                'id' => 'billing',
                'question' => 'Can I switch between monthly and annual billing?',
                'answer' => 'Yes. Choose monthly or annual when you start. Annual billing reduces the per-user monthly rate.',
            ],
            [
                'id' => 'trial-pricing',
                'question' => 'Do plans include a free trial?',
                'answer' => 'Starter and Professional include a free trial so you can evaluate Algos before paying. Enterprise starts with a guided demo.',
            ],
            [
                'id' => 'limits',
                'question' => 'What happens if we outgrow a plan?',
                'answer' => 'You can move up anytime. User and feature limits expand with Professional and Enterprise.',
            ],
            [
                'id' => 'placeholders',
                'question' => 'Are these final prices?',
                'answer' => 'Not yet. Current numbers are placeholders for launch planning. Final billing will connect to live plans later.',
            ],
            [
                'id' => 'future',
                'question' => 'Will more plans be added?',
                'answer' => 'Possibly. Future tiers (for example industry packs or add-ons) may appear as placeholders as the product roadmap expands.',
            ],
        ],
        'future_note' => 'Future add-ons—such as advanced automation packs or industry templates—may appear here as the roadmap grows.',
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

    /*
    |--------------------------------------------------------------------------
    | Features page
    |--------------------------------------------------------------------------
    */

    'features_page' => [
        'headline' => 'Every module your revenue team needs',
        'subheadline' => 'Algos covers the full CRM lifecycle—from first lead to closed customer—with clear permissions, reporting, and multi-tenant control.',
        'groups' => [
            [
                'id' => 'sales',
                'title' => 'Sales & relationships',
                'description' => 'Capture demand and keep customer context after the win.',
                'modules' => [
                    [
                        'icon' => 'user-plus',
                        'title' => 'Lead management',
                        'description' => 'Track every inquiry from capture to conversion with owners, stages, and follow-ups.',
                        'highlights' => [
                            'Pipeline stages and ownership',
                            'Activity history on every lead',
                            'Convert leads to customers in one step',
                        ],
                    ],
                    [
                        'icon' => 'users',
                        'title' => 'Customer management',
                        'description' => 'Maintain account records, timelines, and ownership after deals close.',
                        'highlights' => [
                            'Central customer profiles',
                            'Timeline of related activity',
                            'Company-scoped customer data',
                        ],
                    ],
                    [
                        'icon' => 'building',
                        'title' => 'Company management',
                        'description' => 'Organize workspaces, branding, and tenant settings for each company.',
                        'highlights' => [
                            'Per-company configuration',
                            'Plan and limit awareness',
                            'Clean admin controls',
                        ],
                    ],
                ],
            ],
            [
                'id' => 'execution',
                'title' => 'Execution & collaboration',
                'description' => 'Keep the team aligned on what to do next.',
                'modules' => [
                    [
                        'icon' => 'check-square',
                        'title' => 'Task management',
                        'description' => 'Assign follow-ups, track due dates, and close the loop on sales work.',
                        'highlights' => [
                            'Statuses and assignees',
                            'Due-date visibility',
                            'List and detail views',
                        ],
                    ],
                    [
                        'icon' => 'kanban',
                        'title' => 'Kanban boards',
                        'description' => 'Drag-and-drop boards for leads and tasks so progress stays visual.',
                        'highlights' => [
                            'Lead and task boards',
                            'Quick stage updates',
                            'Shared team visibility',
                        ],
                    ],
                    [
                        'icon' => 'bell',
                        'title' => 'Notifications',
                        'description' => 'Stay informed when follow-ups are due and important events happen.',
                        'highlights' => [
                            'In-app notification center',
                            'Mark read / read all',
                            'Actionable reminders',
                        ],
                    ],
                    [
                        'icon' => 'search',
                        'title' => 'Global search',
                        'description' => 'Find leads, customers, tasks, and users instantly across your workspace.',
                        'highlights' => [
                            'Fast workspace search',
                            'Suggestions as you type',
                            'Permission-aware results',
                        ],
                    ],
                ],
            ],
            [
                'id' => 'insights',
                'title' => 'Insights & data',
                'description' => 'Understand performance and move data in or out with confidence.',
                'modules' => [
                    [
                        'icon' => 'layout-dashboard',
                        'title' => 'Dashboard analytics',
                        'description' => 'See pipeline health, workloads, and KPIs the moment you sign in.',
                        'highlights' => [
                            'At-a-glance KPIs',
                            'Pipeline snapshots',
                            'Team-ready overview',
                        ],
                    ],
                    [
                        'icon' => 'bar-chart',
                        'title' => 'Reports',
                        'description' => 'Filter and export the metrics that matter for revenue operations.',
                        'highlights' => [
                            'Filtered reporting views',
                            'Export-ready outputs',
                            'Ops-friendly insights',
                        ],
                    ],
                    [
                        'icon' => 'file-up',
                        'title' => 'CSV import / export',
                        'description' => 'Migrate and extract leads, customers, tasks, and users without friction.',
                        'highlights' => [
                            'Sample CSV templates',
                            'Bulk import flows',
                            'List exports on demand',
                        ],
                    ],
                    [
                        'icon' => 'scroll-text',
                        'title' => 'Activity logs',
                        'description' => 'Audit important changes across the workspace for accountability.',
                        'highlights' => [
                            'Searchable event history',
                            'User and entity context',
                            'Operational transparency',
                        ],
                    ],
                ],
            ],
            [
                'id' => 'platform',
                'title' => 'Platform & administration',
                'description' => 'Security, tenancy, and control built into the product.',
                'modules' => [
                    [
                        'icon' => 'shield',
                        'title' => 'Role & permission management',
                        'description' => 'Grant precise access so every teammate sees only what they should.',
                        'highlights' => [
                            'Custom roles per company',
                            'Granular permissions',
                            'Safe admin workflows',
                        ],
                    ],
                    [
                        'icon' => 'layers',
                        'title' => 'Multi-tenant architecture',
                        'description' => 'Isolate each company’s data and settings on a shared platform.',
                        'highlights' => [
                            'Company-scoped records',
                            'Tenant-safe queries',
                            'Scalable workspace model',
                        ],
                    ],
                    [
                        'icon' => 'crown',
                        'title' => 'Super Admin',
                        'description' => 'Operate the platform across companies with dedicated admin tools.',
                        'highlights' => [
                            'Company oversight',
                            'Platform settings',
                            'Impersonation controls',
                        ],
                    ],
                ],
            ],
        ],
    ],

];

