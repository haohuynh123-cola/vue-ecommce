<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Audit Implementation
    |--------------------------------------------------------------------------
    |
    | Define which Audit model implementation should be used.
    |
    */

    'enabled' => env('AUDIT_ENABLED', true),
    'implementation' => OwenIt\Auditing\Models\Audit::class,

    /*
    |--------------------------------------------------------------------------
    | User Morph prefix & Guards
    |--------------------------------------------------------------------------
    |
    | Define the morph prefix and authentication guards for the user resolver.
    |
    */

    'user' => [
        'morph_prefix' => 'user',
        'guards' => [
            'web',
            'api',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Resolvers
    |--------------------------------------------------------------------------
    |
    | Define the User, URL, IP Address, User Agent and Tags resolvers.
    |
    */

    'resolver' => [
        'user' => function () {
            return auth()->check() ? auth()->user() : null;
        },
        'url' => function () {
            return request()->fullUrl();
        },
        'ip_address' => function () {
            return request()->ip();
        },
        'user_agent' => function () {
            return request()->userAgent();
        },
        'tags' => function () {
            return [];
        },
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Events
    |--------------------------------------------------------------------------
    |
    | The Eloquent events that trigger an audit.
    |
    */

    'events' => [
        'created',
        'updated',
        'deleted',
        'restored',
    ],

    /*
    |--------------------------------------------------------------------------
    | Strict Mode
    |--------------------------------------------------------------------------
    |
    | Enable strict mode when auditing?
    |
    | When enabled, the audit will fail if the auditable model doesn't
    | implement the Auditable interface.
    |
    */

    'strict' => false,

    /*
    |--------------------------------------------------------------------------
    | Audit Timestamps
    |--------------------------------------------------------------------------
    |
    | Should the audit timestamps be audited?
    |
    */

    'timestamps' => true,

    /*
    |--------------------------------------------------------------------------
    | Audit Threshold
    |--------------------------------------------------------------------------
    |
    | Specify a threshold for the amount of audit records a model can have.
    | Zero means no limit.
    |
    */

    'threshold' => 0,

    /*
    |--------------------------------------------------------------------------
    | Audit Driver
    |--------------------------------------------------------------------------
    |
    | The default audit driver used to keep track of changes.
    |
    */

    'driver' => 'database',

    /*
    |--------------------------------------------------------------------------
    | Audit Drivers
    |--------------------------------------------------------------------------
    |
    | Available audit drivers and their configurations.
    |
    */

    'drivers' => [
        'database' => [
            'table' => 'audits',
            'connection' => null,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Audit Console
    |--------------------------------------------------------------------------
    |
    | Whether console events should be audited (e.g. php artisan db:seed).
    |
    */

    'console' => true,
];
