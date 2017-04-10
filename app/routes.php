<?php

//$app->get('/test/{sub-id}', function ($req, $res, $arg) {
//    $subId = $arg['sub-id'];
//    $subBody = $this->jotform->getSubmission($subId);
//    $this->jotform->editSubmission($subId, ['348' => 'SI']);
//    return $res->withJSON($subBody);
//});
//$app->get('/instalar', function ($req, $res, $arg) {
//    $installer = new \App\Util\Installer($this->db);
//    $installer->up();
//    return $res->withJSON(['mensaje' => 'instalación exitosa.']);
//});

$app->get('/', 'HomeAction:viewHome')->setName('homeGet');
$app->get('/cargar-proyecto', 'HomeAction:loadProject')->setName('proLoadGet');
$app->get('/nuevo-proyecto/{sub-id}', 'HomeAction:newProject')->setName('proNewGet');
$app->get('/facebook-login', 'HomeAction:facebookLogin')->setName('fbCallbackGet');
$app->post('/logout', 'HomeAction:logout')->setName('logoutPost');

//TODO controlar login
$app->get('/registrar-proyecto', 'HomeAction:registerProject')->setName('proRegisterGet');
$app->get('/proyecto', 'ProjectAction:listProjects')->setName('proListGet');
$app->get('/proyecto/{pro}/imagen', 'HomeAction:viewSetImageProject')->setName('proSetImgGet');
$app->post('/proyecto/{pro}/imagen', 'HomeAction:setImageProject')->setName('proSetImgPost');
$app->get('/proyecto/{pro}', 'ProjectAction:viewProject')->setName('proViewGet');
$app->post('/proyecto/{pro}/comentario', 'ProjectAction:commentProject')->setName('proCommentPost');
$app->post('/proyecto/{pro}/voto', 'ProjectAction:voteProject')->setName('proVotePost');
$app->post('/comentario/{com}/respuesta', 'ProjectAction:replyComment')->setName('comReplyPost');
$app->post('/comentario/{com}/voto', 'ProjectAction:rateComment')->setName('comVotePost');
