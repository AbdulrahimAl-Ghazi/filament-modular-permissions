<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Role Resource Configuration
    |--------------------------------------------------------------------------
    |
    | Here you can customize the generated RoleResource.
    |
    */
    /*
    |--------------------------------------------------------------------------
    | Global Protection & Hiding
    |--------------------------------------------------------------------------
    |
    | If enabled, the package will automatically hide resources and widgets
    | from the navigation if the user doesn't have the required permission,
    | without needing to add traits to every file.
    |
    */
    'auto_hide_resources' => true,

    'role_resource' => [
        'navigation_group' => 'Settings',
        'navigation_icon' => 'heroicon-o-shield-check',
        'navigation_label' => null,
        'cluster' => null,
    ],

    'user_resource' => [
        'navigation_group' => 'Settings',
        'navigation_icon' => 'heroicon-o-users',
        'navigation_label' => null,
        'cluster' => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Super Admin Configuration
    |--------------------------------------------------------------------------
    */
    'super_admin_role_name' => 'super_admin',

    /*
    |--------------------------------------------------------------------------
    | Permissions Actions
    |--------------------------------------------------------------------------
    |
    | These are the actions that will be synced for each resource.
    |
    */
    'actions' => [
        'view_any',
        'view',
        'create',
        'update',
        'delete',
        'delete_any',
        'force_delete',
        'force_delete_any',
        'restore',
        'restore_any',
        'reorder',
        'replicate',
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Translations (Quick Overrides)
    |--------------------------------------------------------------------------
    |
    | You can quickly override specific translations here. If empty, 
    | the package will use the language files in lang/*.php.
    |
    */
    'translations' => [
        'actions' => [
            // 'view_any' => 'Custom Text',
        ],
        'resources' => [
            // 'user_resource' => 'Users Section',
        ],
        'widgets' => [
            'dashboard_widgets_label' => null, // Set a string to override
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Generator Settings
    |--------------------------------------------------------------------------
    |
    | Define how the files should be generated.
    |
    */
    'generator' => [
        'namespace' => 'App\\Filament\\Resources\\Roles',
        'path' => app_path('Filament/Resources/Roles'),
    ],
];
