<?php

$dotenv = new Dotenv\Dotenv(__DIR__ . '/../');
$dotenv->load();

return [
    'settings' => [
        'displayErrorDetails' => false, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => isset($_ENV['docker']) ? 'php://stdout' : __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],

        'basicAuth' => [
            "secure" => false,
            'users' => [
                getenv('user') => getenv('password')
            ]
        ]
    ],
];
