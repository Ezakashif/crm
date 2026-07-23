<?php

use App\Notifications\CustomerCreated;
use App\Notifications\LeadAssigned;
use App\Notifications\TaskAssigned;
use App\Notifications\WebsiteLeadReceived;

return [
    'channels' => [
        'database' => 'In-app',
        'email' => 'Email',
    ],

    'types' => [
        'lead_assigned' => [
            'class' => LeadAssigned::class,
            'label' => 'Lead assignments',
            'description' => 'When a lead is assigned to you.',
        ],
        'task_assigned' => [
            'class' => TaskAssigned::class,
            'label' => 'Task assignments',
            'description' => 'When a task is assigned to you.',
        ],
        'customer_created' => [
            'class' => CustomerCreated::class,
            'label' => 'New customers',
            'description' => 'When a customer is created in your company.',
        ],
        'website_lead_received' => [
            'class' => WebsiteLeadReceived::class,
            'label' => 'Website leads',
            'description' => 'When a website lead is received for your company.',
        ],
    ],
];
