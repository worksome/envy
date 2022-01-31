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
     * Calls to the `env` function can provide a second parameter that will act as a
     * default when no matching variable is found in your .env file. Enabling this
     * option will also insert the default in your .env file when copying a value.
     */
    'display_default_values' => true,

];
