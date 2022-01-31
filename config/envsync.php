<?php

declare(strict_types=1);

return [

    'environment_files' => [
        base_path('.env.example'),
    ],

    'config_files' => [
        config_path(),
    ],

    'display_comments' => false,

    'display_location_hints' => false,

    'display_default_values' => true,

];
