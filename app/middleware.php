<?php
$container = $app->getContainer();

$app->add(new Adbar\SessionMiddleware($container->get('settings')['session']));
$app->add($container->get('csrf'));
