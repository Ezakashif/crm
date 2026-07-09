<?php

/**
 * Model-driven permission registry.
 *
 * Each module defines available actions. Permissions are auto-synced as
 * {action}.{module} (e.g. view.customers, create.tasks).
 *
 * To add a new module: add an entry here and run `php artisan permissions:sync`.
 */
return [
    'modules' => [
        'customers' => [
            'label' => 'Customers',
            'actions' => [
                'view' => 'View',
                'create' => 'Create',
                'update' => 'Update',
                'delete' => 'Delete',
            ],
        ],
        'leads' => [
            'label' => 'Leads',
            'actions' => [
                'view' => 'View Own',
                'view_all' => 'View All',
                'create' => 'Create',
                'update' => 'Update',
                'delete' => 'Delete',
                'assign' => 'Assign',
                'convert' => 'Convert',
                'log' => 'Log Activities',
            ],
        ],
        'tasks' => [
            'label' => 'Tasks',
            'actions' => [
                'view' => 'View Own',
                'view_all' => 'View All',
                'create' => 'Create',
                'change_status' => 'Change Status',
                'update' => 'Update',
                'delete' => 'Delete',
                'assign' => 'Assign',
            ],
        ],
        'users' => [
            'label' => 'Users',
            'actions' => [
                'view' => 'View',
                'create' => 'Create',
                'update' => 'Update',
                'delete' => 'Delete',
            ],
        ],
        'roles' => [
            'label' => 'Roles',
            'actions' => [
                'view' => 'View',
                'create' => 'Create',
                'update' => 'Update',
                'delete' => 'Delete',
            ],
        ],
        'activity_logs' => [
            'label' => 'Activity Logs',
            'actions' => [
                'view' => 'View All',
                'view_own' => 'View Own',
            ],
        ],
        'demo' => [
            'label' => 'Demo',
            'actions' => [
                'website_lead' => 'Website Lead',
            ],
        ],
    ],
];
