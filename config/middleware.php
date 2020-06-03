<?php

use Slim\App;
use Slim\Middleware\ErrorMiddleware;
use Slim\Views\TwigMiddleware;
use Slim\Middleware\Session;

return function (App $app) {
    // Parse json, form data and xml
    $app->addBodyParsingMiddleware();

    // Twig Middleware
    $app->add(TwigMiddleware::class);

    // Slim Session Middleware
    $app->add(
        new Session([
            'name' => 'dummy_session',
            'autorefresh' => true,
            'lifetime' => '1 hour',
        ])
    );

    // Add the Slim built-in routing middleware
    $app->addRoutingMiddleware();

    // Catch exceptions and errors
    $app->add(ErrorMiddleware::class);
};
