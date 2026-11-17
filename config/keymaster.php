<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Age and consent
    |--------------------------------------------------------------------------
    |
    | Users younger than this age who register themselves are created in the
    | "pending_consent" state and must be approved by a parent or guardian
    | before they can use saved features. Confirm the right age for each
    | launch country with legal advice (13 under COPPA; 13-16 under GDPR).
    |
    */

    'consent_age' => (int) env('KEYMASTER_CONSENT_AGE', 13),

    'minimum_age' => 5,

    'consent_link_expires_days' => 14,

    /*
    |--------------------------------------------------------------------------
    | Teacher accounts
    |--------------------------------------------------------------------------
    |
    | Teacher access must be approved by an administrator, unless the user's
    | verified email belongs to one of these school domains.
    |
    */

    'teacher_auto_approve_domains' => array_values(array_filter(array_map(
        'trim',
        explode(',', (string) env('KEYMASTER_TEACHER_EMAIL_DOMAINS', '')),
    ))),

    /*
    |--------------------------------------------------------------------------
    | Database accounts
    |--------------------------------------------------------------------------
    |
    | Used by `php artisan keymaster:db-grants` to create the least-privilege
    | runtime account. Tables listed as append-only receive SELECT and INSERT
    | only, so the running application can never rewrite history.
    |
    */

    'database' => [
        'app_username' => env('DB_APP_USERNAME', 'keymaster_app'),
        'app_password' => env('DB_APP_PASSWORD'),
        'app_host' => env('DB_APP_HOST', '%'),

        'append_only_tables' => [
            'xp_transactions',
            'typing_attempts',
            'typing_attempt_logs',
            'attempt_moderations',
            'audit_logs',
        ],

        // Framework tables the runtime account never needs.
        'excluded_tables' => [
            'migrations',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Defaults
    |--------------------------------------------------------------------------
    */

    'default_timezone' => 'UTC',

    'default_daily_goal_minutes' => 10,

    /*
    |--------------------------------------------------------------------------
    | Advertising (Phase 7)
    |--------------------------------------------------------------------------
    |
    | Never hardcode the publisher ID. Ads are always off outside production.
    |
    */

    'ads' => [
        'enabled' => (bool) env('ADS_ENABLED', false),
        'client_id' => env('ADSENSE_CLIENT_ID'),
    ],

];
