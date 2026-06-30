<?php

return [
    'default' => env('FILESYSTEM_DISK', 'local'),

    'disks' => [
        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
            'throw' => false,
        ],

        /*
        | Writes uploaded files DIRECTLY into the public_html/uploads folder
        | so they are served by Apache without symlinks. Relative to the
        | Laravel project root: ../public_html/uploads
        | (cPanel layout: /home/<user>/laravel + /home/<user>/public_html)
        */
        'public_html' => [
            'driver' => 'local',
            'root' => base_path('../public_html/uploads'),
            'url' => env('APP_URL') . '/uploads',
            'visibility' => 'public',
            'throw' => false,
        ],
    ],

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],
];
