<?php

use Cake\Log\Engine\FileLog;

$config = [
    'debug' => filter_var(env('DEBUG', true), FILTER_VALIDATE_BOOLEAN),

    'Security' => [
        'salt' => env('SECURITY_SALT', '__SALT__'),
    ],

    'EmailTransport' => [
        'default' => [
            'className' => 'Mail',
            // The following keys are used in SMTP transports
            'host' => 'localhost',
            'port' => 25,
            'timeout' => 30,
            'username' => 'no-reply@theether.com',
            'password' => env('EMAIL_PASSWORD', null),
            'client' => null,
            'tls' => null,
            'url' => env('EMAIL_TRANSPORT_DEFAULT_URL', null),
        ],
    ],

    'Datasources' => [
        'default' => [
            'className' => 'Cake\Database\Connection',
            'driver' => 'Cake\Database\Driver\Mysql',
            'persistent' => false,
            'host' => 'localhost',
            /**
             * CakePHP will use the default DB port based on the driver selected
             * MySQL on MAMP uses port 8889, MAMP users will want to uncomment
             * the following line and set the port accordingly
             */
            //'port' => 'non_standard_port_number',
            'username' => env('DATABASE_USERNAME', null),
            'password' => env('DATABASE_PASSWORD', null),
            'database' => env('DATABASE_NAME', null),
            'encoding' => 'utf8',
            'timezone' => 'UTC',
            'flags' => [],
            'cacheMetadata' => true,
            'log' => false,

            /**
             * Set identifier quoting to true if you are using reserved words or
             * special characters in your table or column names. Enabling this
             * setting will result in queries built using the Query Builder having
             * identifiers quoted when creating SQL. It should be noted that this
             * decreases performance because each query needs to be traversed and
             * manipulated before being executed.
             */
            'quoteIdentifiers' => false,

            /**
             * During development, if using MySQL < 5.6, uncommenting the
             * following line could boost the speed at which schema metadata is
             * fetched from the database. It can also be set directly with the
             * mysql configuration directive 'innodb_stats_on_metadata = 0'
             * which is the recommended value in production environments
             */
            //'init' => ['SET GLOBAL innodb_stats_on_metadata = 0'],

            'url' => env('DATABASE_URL', null),
        ],

        /**
         * The test connection is used during the test suite.
         */
        'test' => [
            'className' => 'Cake\Database\Connection',
            'driver' => 'Cake\Database\Driver\Mysql',
            'persistent' => false,
            'host' => 'localhost',
            //'port' => 'non_standard_port_number',
            'username' => 'root',
            'password' => false,
            'database' => 'test_myapp',
            'encoding' => 'utf8',
            'timezone' => 'UTC',
            'cacheMetadata' => true,
            'quoteIdentifiers' => false,
            'log' => false,
            //'init' => ['SET GLOBAL innodb_stats_on_metadata = 0'],
            'url' => env('DATABASE_TEST_URL', null),
        ],
    ],

    'Email' => [
        'default' => [
            'transport' => 'default',
            'from' => ['no-reply@theether.com' => 'Ether'],
            'sender' => ['no-reply@theether.com' => 'Ether'],
            'emailFormat' => 'both',
            'charset' => 'utf-8',
            'headerCharset' => 'utf-8',
        ],
        'new_message' => [
            'transport' => 'default',
            'from' => ['no-reply@theether.com' => 'Ether'],
            'sender' => ['no-reply@theether.com' => 'Ether'],
            'emailFormat' => 'both',
            'charset' => 'utf-8',
            'headerCharset' => 'utf-8',
            'template' => 'new_message'
        ],
        'reset_password' => [
            'transport' => 'default',
            'from' => ['no-reply@theether.com' => 'Ether'],
            'sender' => ['no-reply@theether.com' => 'Ether'],
            'subject' => 'Ether Account Password Reset',
            'emailFormat' => 'both',
            'charset' => 'utf-8',
            'headerCharset' => 'utf-8',
            'template' => 'reset_password'
        ]
    ],

    'Log' => [
        'email' => [
            'className' => FileLog::class,
            'path' => LOGS,
            'file' => 'email',
            'levels' => ['info'],
        ]
    ],

    'Session' => [
        'cookie' => 'ether'
    ],

    'Asset' => [
        'timestamp' => 'force',
    ],

    'Cache' => [
        'long' => [
            'className' => 'File',
            'duration' => '+1 week',
            'probability' => 100,
            'path' => CACHE . 'long' . DS,
        ],
    ],

    'Recaptcha' => [
        'sitekey' => '6Ldw9D4UAAAAALHquIqLYectG-dyzbJylqdScr5r',
        'secret' => env('RECAPTCHA_SECRET'),
        'lang' => 'en',
        'theme' => 'dark', // either light or dark
        'type' => 'image', // either image or audio
        'size' => 'normal' // either normal or compact
    ],

    'no_reply_email' => 'no-reply@theether.com'
];

// Stripe
$stripeMode = env('STRIPE_MODE', 'Live'); // Test or Live
$config['Stripe.mode'] = $stripeMode;
$config['Stripe.TestSecret'] = env('STRIPE_TEST_SECRET');
$config['Stripe.TestPublic'] = env('STRIPE_TEST_PUBLIC');
$config['Stripe.LiveSecret'] = env('STRIPE_LIVE_SECRET');
$config['Stripe.LivePublic'] = env('STRIPE_LIVE_PUBLIC');
$stripeSecret = $config["Stripe.{$stripeMode}Secret"];
$stripePublic = $config["Stripe.{$stripeMode}Public"];
$config['Stripe.Secret'] = $stripeSecret;
$config['Stripe.Public'] = $stripePublic;

return $config;
