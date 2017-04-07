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
    return function ($req, $res, $e) use ($c) {
        $json = (substr($req->getHeaderLine('Accept'), 0, 16) == 'application/json');
        $stat = 200;
        $mssg = 'Ocurrió un error interno.';
        $c->get('logger')->info($e->getMessage());
        if ($e instanceof \Facebook\Exceptions\FacebookResponseException) {
            $stat = 500;
            $mssg = 'En este momento no podemos comunicarnos con Facebook correctamente.';
        } elseif ($e instanceof \Facebook\Exceptions\FacebookSDKException) {
            $stat = 500;
            $mssg = 'Facebook no nos permite acceder a tu cuenta en este momento.';
        } elseif ($e instanceof \Illuminate\Database\Eloquent\ModelNotFoundException) {
            $stat = 404;
            $mssg = 'Recurso no encontrado.';
        } elseif ($e instanceof \App\Util\AppException) {
            $stat = $e->getCode();
            $mssg = $e->getMessage();
        }
        if ($json) {
            return $res->withStatus($stat)->withJSON([
                'mensaje' => $mssg,
            ]);
        } else {
            return $c->get('view')->render($res, 'master.twig', [
                'mensaje' => $mssg,
            ]);
        }
    };
};
