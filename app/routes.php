<?php

$app->get('/test/{sub-id}', function ($req, $res, $arg) {
    $subId = $arg['sub-id'];
    $subBody = $this->jotform->getSubmission($subId);
    return $res->withJSON($subBody);
});

$app->get('/nuevo-proyecto/{sub-id}', 'HomeAction:newProject')->setName('proNewGet');
$app->get('/facebook-login', 'HomeAction:facebookLogin')->setName('fbCallbackGet');
//TODO controlar login
$app->get('/registrar-proyecto', 'HomeAction:registerProject')->setName('proRegisterGet');

$app->get('/greet/[{name}]', 'App\ExampleController:greet')->setName('greet');
$app->get('/send-mail', 'App\ExampleController:sendMail');
$app->get('/query-db', 'App\ExampleController:queryDB');
