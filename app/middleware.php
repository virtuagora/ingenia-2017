<?php
$settings = $app->getContainer()->get('settings');

$app->add(new Adbar\SessionMiddleware($settings['session']));
