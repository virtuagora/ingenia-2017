<?php

$app->get('/test/{sub-id}', function ($req, $res, $arg) {
    $subId = $arg['sub-id'];
    $subBody = $this->jotform->getSubmission($subId);
    return $res->withJSON($subBody);
});
$app->get('/instalar', function ($req, $res, $arg) {
    $installer = new \App\Util\Installer($this->db);
    $installer->up();
    return $res->withJSON(['mensaje' => 'instalación exitosa.']);
});

$app->get('/nuevo-proyecto/{sub-id}', 'HomeAction:newProject')->setName('proNewGet');
$app->get('/facebook-login', 'HomeAction:facebookLogin')->setName('fbCallbackGet');
//TODO controlar login
$app->get('/registrar-proyecto', 'HomeAction:registerProject')->setName('proRegisterGet');
$app->get('/proyecto/{pro}/imagen', 'HomeAction:viewSetImageProject')->setName('proSetImgGet');
$app->post('/proyecto/{pro}/imagen', 'HomeAction:setImageProject')->setName('proSetImgPost');
$app->get('/proyecto/{pro}', 'HomeAction:viewProject')->setName('proViewGet');

$app->get('/greet/[{name}]', 'App\ExampleController:greet')->setName('greet');
$app->get('/send-mail', 'App\ExampleController:sendMail');
$app->get('/query-db', 'App\ExampleController:queryDB');
