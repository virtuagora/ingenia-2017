<?php
$container = $app->getContainer();

$app->add($container->get('csrf'));
$app->add(new Adbar\SessionMiddleware($container->get('settings')['session']));
