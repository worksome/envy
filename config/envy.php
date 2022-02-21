<?php

declare(strict_types=1);

return [

    /*
     * Here, you should include any environment files that you want to keep
     * in sync. For most projects, the `.env.example` file will suffice.
     * However, you're free to include more as your project requires.
     */
    'environment_files' => [
        base_path('.env.example'),
    ],

    /**
     * Here you should list any config files/directories that you want to be
     * included when looking for calls to `env`. Directories are searched
     * recursively. Feel free to include unpublished vendor configs too.
     */
    'config_files' => [
        config_path(),
    ],

    /**
     * Comments like the one you're reading can be quite useful when trying
     * to remember what an environment variable is used for. When set to
     * true, we'll copy any comments we find in config over to .env.
     */
    'display_comments' => false,

    /**
     * Some developers find it useful to have reference to where an environment
     * variable is used. Enabling this option will display a comment above a
     * linked .env variable with reference to the correct config file.
     */
    'display_location_hints' => false,

    /**
     * Enabling this option will also insert any provided defaults in your .env file
     * when updating. Note that only scalar (primitive) types will be copied over.
     * Defaults that include spaces will be wrapped in quotes for you.
     *
     * Note that `exclude_calls_with_defaults` must be set to `false` for this
     * to take effect.
     */
    'display_default_values' => true,

    /**
     * When calling the `env` function, you can optionally provide a default as the
     * second parameter. Envy will ignore any calls with a set default if this
     * option is set to true. Otherwise, it will include them whilst syncing.
     */
    'exclude_calls_with_defaults' => true,

    /**
     * Any environment variables that are added to exclusions will never be inserted
     * into .env files. Our defaults are based on the base Laravel config files.
     * Feel free to add or remove variables as required by your project needs.
     */
    'exclusions' => [
        // config/app.php
        'ASSET_URL',

        // config/broadcasting.php
        'ABLY_KEY',

        // config/cache.php
        'MEMCACHED_PERSISTENT_ID',
        'MEMCACHED_USERNAME',
        'MEMCACHED_PASSWORD',
        'MEMCACHED_HOST',
        'MEMCACHED_PORT',
        'AWS_ACCESS_KEY_ID',
        'AWS_SECRET_ACCESS_KEY',
        'AWS_DEFAULT_REGION',
        'DYNAMODB_CACHE_TABLE',
        'DYNAMODB_ENDPOINT',

        // config/database.php
        'DATABASE_URL',
        'DB_SOCKET',
        'REDIS_CLIENT',
        'REDIS_CLUSTER',
        'REDIS_PREFIX',
        'REDIS_URL',
        'REDIS_DB',
        'REDIS_CACHE_DB',
        'MYSQL_ATTR_SSL_CA',

        // config/filesystem.php
        'AWS_ENDPOINT',
        'AWS_URL',

        // config/hashing.php
        'BCRYPT_ROUNDS',

        // config/logging.php
        'PAPERTRAIL_URL',
        'PAPERTRAIL_PORT',
        'LOG_STDERR_FORMATTER',
        'LOG_SLACK_WEBHOOK_URL',

        // config/mail.php
        'MAIL_SENDMAIL_PATH',
        'MAIL_LOG_CHANNEL',
        'MAIL_FROM_ADDRESS',
        'MAIL_FROM_NAME',

        // config/queue.php
        'SQS_PREFIX',
        'SQS_QUEUE',
        'SQS_SUFFIX',
        'REDIS_QUEUE',
        'QUEUE_FAILED_DRIVER',

        // config/sanctum.php
        'SANCTUM_STATEFUL_DOMAINS',

        // config/services.php
        'MAILGUN_DOMAIN',
        'MAILGUN_SECRET',
        'MAILGUN_ENDPOINT',
        'POSTMARK_TOKEN',

        // config/session.php
        'SESSION_CONNECTION',
        'SESSION_STORE',
        'SESSION_COOKIE',
        'SESSION_DOMAIN',
        'SESSION_SECURE_COOKIE',

        // config/view.php
        'VIEW_COMPILED_PATH',
    ],

    /**
     * Any environment variables that are added to inclusions will never be pruned from
     * your .env files. By default, we include Laravel Mix variables. Feel free to
     * add or remove environment variables to suit your project's requirements.
     */
    'inclusions' => [
        'MIX_PUSHER_APP_KEY',
        'MIX_PUSHER_APP_CLUSTER',
    ],
];
