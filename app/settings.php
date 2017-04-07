<?php
return [
    'settings' => [
        'mode' => 'dev',
        'determineRouteBeforeAppMiddleware' => true,
        'displayErrorDetails' => true,
        'timezone' => 'America/Argentina/Buenos_Aires',
        'twig' => [
            'path' => __DIR__ . '/templates',
            'options' => [
                'cache' => false, //__DIR__ . '/../var/cache',
                //'debug' => true,
            ]
        ],
        'swiftmailer' => [
            'transport' => 'mail',
            'options' => [
                'host' => 'localhost',
                'port' => 25,
                'username' => '',
                'password' => '',
            ]
        ],
        'monolog' => [
            'name' => 'monolog',
            'path' => __DIR__ . '/../var/logs/app.log',
        ],
        'eloquent' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'ingenia',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
            'collation' => 'utf8_general_ci',
            'prefix' => '',
        ],
        'facebook' => [
            'app_id' => '185937511894838',
            'app_secret' => 'e80b1c6188ad60b1ea641066b2b660a4',
            'default_graph_version' => 'v2.8',
        ],
        'jotform' => [
            'api_key' => '399be6f87f8d8428b751787e6bdf4cf4',
        ],
        'session' => [
            'name'           => 'virtuagora',
            'lifetime'       => 24,
            'path'           => '/',
            'domain'         => null,
            'secure'         => false,
            'httponly'       => true,
            'cookie_autoset' => true,
            'save_path'      => null,
            'cache_limiter'  => 'nocache',
            'autorefresh'    => false,
            'encryption_key' => null,
            'namespace'      => 'virtuagora',
        ],
        'filesystem' => [
            'adapter' => 'League\Flysystem\Adapter\Local',
            'args' => [
                __DIR__ . '/../public/files',
            ],
        ],
    ],
];
