<?php
// Application middleware

// e.g: $app->add(new \Slim\Csrf\Guard);

$container['basic_auth_middleware'] = function (\Slim\Container $c) {
    $setting = $c->get('settings')['basicAuth'];
    $middleware = new \Slim\Middleware\HttpBasicAuthentication($setting);
    return $middleware;
};
