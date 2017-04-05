<?php

use Respect\Validation\Validator as v;

$container = $app->getContainer();

$container['csrf'] = function ($c) {
    return new \Slim\Csrf\Guard();
};
$container['flash'] = function () {
    return new \Slim\Flash\Messages();
};
$container['db'] = function ($c) {
    $settings = $c->get('settings')['eloquent'];
    return new \App\Util\EloquentService($settings);
};
$container['logger'] = function ($c) {
    $settings = $c->get('settings')['monolog'];
    $logger = new \Monolog\Logger($settings['name']);
    $logger->pushProcessor(new \Monolog\Processor\UidProcessor());
    $logger->pushHandler(new \Monolog\Handler\StreamHandler($settings['path'], \Monolog\Logger::DEBUG));
    return $logger;
};
$container['mailer'] = function ($c) {
    $settings = $c->get('settings')['swiftmailer'];
    $mailer = new \App\Util\SwiftMailerService(
        $c->get('settings')['mode'],
        $settings['transport'],
        $settings['options']
    );
    return $mailer;
};
$container['helper'] = function ($c) {
    return new \App\Service\Helper($c['router'], $c['request']);
};
$container['view'] = function ($c) {
    $settings = $c->get('settings')['twig'];
    $view = new \Slim\Views\Twig($settings['path'], $settings['options']);
    $view->addExtension(new \App\Util\TwigExtension($c['helper'], $c['csrf']));
    return $view;
};
$container['facebook'] = function ($c) {
    $settings = $c->get('settings')['facebook'];
    return new \Facebook\Facebook($settings);
};
$container['jotform'] = function ($c) {
    $settings = $c->get('settings')['jotform'];
    return new JotForm($settings['api_key']);
};
$container['session'] = function ($c) {
    return new \AdBar\Session('virtuagora');
};
$container['filesystem'] = function ($c) {
    $settings = $c->get('settings')['filesystem'];
    $adapter = new \ReflectionClass($settings['adapter']);
    return new League\Flysystem\Filesystem($adapter->newInstanceArgs($settings['args']));
};
$container['validation'] = function ($c) {
    $commentVdt = v::alnum()->length(2, 5000);
    return [
        'comment' => $commentVdt,
    ];
};

$container['HomeAction'] = function ($c) {
    return new \App\Acion\HomeAction($c);
};

$container['errorHandler'] = function ($c) {
    return function ($request, $response, $exception) use ($c) {
        $c->get('logger')->info($exception->getMessage());
        if ($exception instanceof \Facebook\Exceptions\FacebookResponseException) {
            return $response->withStatus(500)->withJSON([
                'mensaje' => 'En este momento no podemos comunicarnos con Facebook correctamente.',
            ]);
        } elseif ($exception instanceof \Facebook\Exceptions\FacebookSDKException) {
            return $response->withStatus(500)->withJSON([
                'mensaje' => 'acebook no nos permite acceder a tu cuenta en este momento.',
            ]);
        } elseif ($exception instanceof \App\Util\AppException) {
            return $response->withStatus($exception->getCode())->withJSON([
                'mensaje' => $exception->getMessage(),
            ]);
        }
        return $response->withStatus(500)->withJSON([
            'mensaje' => 'Ocurrió un error interno.',
        ]);
    };
};
