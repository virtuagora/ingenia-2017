<?php
$settings = $app->getContainer()->get('settings');

$app->add(new \AdBar\SessionMiddleware($settings['session']));
