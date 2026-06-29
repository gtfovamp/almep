<?php

return [
    'paths' => ['api/*'],
    'allowed_methods' => ['*'],
    // Set your front-end / admin panel origins here, or '*' for any.
    'allowed_origins' => ['*'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => false,
];
