<?php

use Huboo\Translations\Loader\HybridLoader;

return [
    'default' => env('DEFAULT_LANGUAGE', 'en'),

    'loader' => [
        'loader' => HybridLoader::class,
        'url' => env('TRANSLATIONS_SERVICE_S3_DOMAIN'),
    ],
];
