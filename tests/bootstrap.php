<?php
require_once __DIR__.'/../config/bootstrap.php';

if (isset($_ENV['APP_TEST_RELOAD_DB']) && $_ENV['APP_TEST_RELOAD_DB'] === '1') {
    $commands = [
        'doctrine:schema:drop',
        'doctrine:migrations:migrate --no-interaction',
        'doctrine:fixtures:load --no-interaction',
    ];

    foreach ($commands as $command) {
        passthru(sprintf(
            'php "%s/../bin/console" %s --env=%s',
            __DIR__,
            $command,
            $_ENV['APP_ENV'],
        ));
    }
}

if (isset($_ENV['APP_TEST_CLEAR_CACHE']) && $_ENV['APP_TEST_CLEAR_CACHE'] === '1') {
    // executes the "php bin/console cache:clear" command
    passthru(sprintf(
        'php "%s/../bin/console" cache:clear --env=%s --no-warmup',
        __DIR__,
        $_ENV['APP_ENV']
    ));
}

require __DIR__.'/../vendor/autoload.php';
