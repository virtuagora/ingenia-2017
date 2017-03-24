<?php namespace App\Action;

final class HomeAction extends AbstractAction
{
    public function newProject($req, $res, $arg) {
        $this->logger->info("Hello!");
        $subId = $arg['sub-id'];
        $subBody = $this->jotform->getSubmission($subId);
        //TODO cambiar al form de ingenia
        if ($subBody['form_id'] != '70586122874663') {
            throw new \App\Util\AppException('Bad request.', 400);
        }
        $this->session->set('project', $subBody);
        $helper = $this->facebook->getRedirectLoginHelper();
        $permissions = ['email'];
        $loginUrl = $helper->getLoginUrl(
            $this->helper->completePathFor('fbCallbackGet'),
            $permissions
        );
        return $res->withRedirect($loginUrl);
    }

    public function facebookLogin($req, $res, $arg) {
        $helper = $this->facebook->getRedirectLoginHelper();
        $accessToken = $helper->getAccessToken();
        if (!isset($accessToken)) {
            if ($helper->getError()) {
                $msg = 'Error: '.$helper->getError().'\n';
                $msg += 'Error Code: '.$helper->getErrorCode().'\n';
                $msg += 'Error Reason: '.$helper->getErrorReason().'\n';
                $msg += 'Error Description: '.$helper->getErrorDescription();
                throw new \App\Util\AppException($msg, 401);
            }
            throw new \App\Util\AppException('Bad request.', 400);
        }
        $response = $this->facebook->get('/me?fields=id,name,email', $accessToken);
        $userNode = $response->getGraphUser();
        $user = $this->db->query('App:User')->firstOrCreate([
            'name' => $userNode['name'],
            'facebook' => $userNode['id'],
            'email' => $userNode['email'],
        ]);
        $this->session->set('user', $user->toArray());
        $redirectUrl = $this->session->has('project')?
            $this->helper->completePathFor('proRegisterGet'):
            $this->helper->baseUrl();
        return $res->withRedirect($redirectUrl);
    }

    public function registerProject($req, $res, $arg) {
        if (!$this->session->has('project')) {
            throw new \App\Util\AppException('Bad request.', 400);
        }
        $project = $this->db->query('App:Project')->where('jotform', $this->session->get('project'))->first();
        if ($project) {
            throw new \App\Util\AppException('El proyecto ya está registrado.', 400);
        }
        //TODO cambiar a los campos de ingenia
        $project = $this->db->query('App:Project')->create([
            'name' => $this->session->get('project.answers.4.answer'),
            'jotform' => $this->session->get('project.id'),
            'description' => $this->session->get('project.answers.5.answer'),
            'user_id' => $this->session->get('user.id'),
        ]);
        return $res->withRedirect($this->helper->completePathFor(
            'proPictureGet',
            ['pro' => $project->id]
        ));
    }

    public function viewSetImageProject($req, $res, $arg) {
        $project = $this->db->query('App:Project')->findOrFail($arg['pro']);
        $user = $project->user;
        //TODO ver si el control de acceso se hace mas lindo
        if ($user->id != $this->session->get('user.id')) {
            throw new \App\Util\AppException('Forbiden.', 405);
        }

        return $this->view->render($response, 'master.twig', [
            'project' => $project,
        ]);
    }

    public function setImageProject($req, $res, $arg) {
        $project = $this->db->query('App:Project')->findOrFail($arg['pro']);
        $user = $project->user;
        //TODO ver si el control de acceso se hace mas lindo
        if ($user->id != $this->session->get('user.id')) {
            throw new \App\Util\AppException('Forbiden.', 405);
        }
        
        $files = $req->getUploadedFiles();
        if (empty($files['imagen'])) {
            throw new \App\Util\AppException('No se enviaron los archivos necesarios', 400);
        }
        $imgFile = $files['imagen'];
        if ($imgFile->getError() !== UPLOAD_ERR_OK) {
            throw new \App\Util\AppException('Hubo un error con el archivo firmado recibido', 400);
        }
        $imgStrm = $imgFile->getStream()->detach();
        $this->filesystem->writeStream('img/projects'.$project->id.'.jpg', $imgStrm);
        if (is_resource($imgStrm)) {
            fclose($imgStrm);
        }

        if (!$project->has_image) {
            $project->has_image = true;
            $project->save();
        }

        return $res->withRedirect($this->helper->completePathFor(
            'proPictureGet',
            ['pro' => $project->id]
        ));
    }
}
