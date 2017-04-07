<?php namespace App\Action;

final class HomeAction extends AbstractAction
{
    public function newProject($req, $res, $arg)
    {
        $subId = $arg['sub-id'];
        $subBody = $this->jotform->getSubmission($subId);
        //TODO cambiar al form de ingenia
        if ($subBody['form_id'] != '70946170891665') {
            throw new \App\Util\AppException('Bad request.', 400);
        }

        $formData = [
            'jotform' => $subBody['form_id'],
            'group' => $subBody['project']['answers']['4']['answer'],
            'name' => $subBody['project']['answers']['81']['answer'],
            'description' => $subBody['project']['answers']['82']['answer'],
            'foundation' => $subBody['project']['answers']['83']['answer'],
            'execution' => isset($subBody['project']['answers']['86'])?
                $subBody['project']['answers']['86']['answer']: null,
            'organization' => isset($subBody['project']['answers']['89'])?
                $subBody['project']['answers']['89']['answer']: null,
            'schedule' => $subBody['project']['answers']['96']['answer'],
            'budget' => $subBody['project']['answers']['98']['answer'],
            'total_budget' => $subBody['project']['answers']['99']['answer'],
            'place' => trim($subBody['project']['answers']['179']['answer']),
            'category' => $subBody['project']['answers']['181']['answer'],
        ];
        $this->session->set('project', $subBody);

        if ($this->session->has('user')) {
            return $this->view->render($response, 'master.twig');
        } else {
            $helper = $this->facebook->getRedirectLoginHelper();
            $permissions = ['email'];
            $loginUrl = $helper->getLoginUrl(
                $this->helper->completePathFor('fbCallbackGet'),
                $permissions
            );
            return $this->view->render($response, 'master.twig', [
                'fbLink' => $loginUrl,
            ]);
        }
    }

    public function facebookLogin($req, $res, $arg)
    {
        $helper = $this->facebook->getRedirectLoginHelper();
        $accessToken = $helper->getAccessToken();
        if (!isset($accessToken)) {
            if ($helper->getError()) {
                $msg = 'Error: '.$helper->getError().'\n';
                $msg += 'Error Code: '.$helper->getErrorCode().'\n';
                $msg += 'Error Reason: '.$helper->getErrorReason().'\n';
                $msg += 'Error Description: '.$helper->getErrorDescription();
                $this->logger->info($msg);
            }
            throw new \App\Util\AppException('Petición de acceso a Facebook inválida.', 400);
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

    public function registerProject($req, $res, $arg)
    {
        if (!$this->session->has('project')) {
            throw new \App\Util\AppException('Petición inválida.', 400);
        }
        $project = $this->db->query('App:Project')->where('jotform', $this->session->get('project'))->first();
        if ($project) {
            throw new \App\Util\AppException('El proyecto ya está registrado.', 400);
        }

        $categoryList = array_flip($this->helper->getCategories());

        $project = new \App\Model\Project();
        $project->name = $this->session->get('project.name');
        $project->jotform = $this->session->get('project.form_id');
        $project->group = $this->session->get('project.group');
        $project->description = $this->session->get('project.description');
        $project->foundation = $this->session->get('project.foundation');
        $project->execution = $this->session->get('project.execution');
        $project->organization = $this->session->get('project.organization');
        $project->total_budget = $this->session->get('project.total_budget');
        $project->place = $this->session->get('project.place');
        if (isset($categoryList[$this->session->get('project.category')])) {
            $project->category = $categoryList[$this->session->get('project.category')];
        } else {
            $project->category = 'social';
        }

        $project->budget = $this->session->get('project.budget');
        $project->schedule = $this->session->get('project.schedule');

        $project->name_trace = $this->helper->generateTrace($project->name);
        $project->group_trace = $this->helper->generateTrace($project->group);
        $project->save();
        /* cambiar a los campos de ingenia
        $project = $this->db->query('App:Project')->create([
            'name' => $this->session->get('project.answers.4.answer'),
            'jotform' => $this->session->get('project.id'),
            'description' => $this->session->get('project.answers.5.answer'),
            'user_id' => $this->session->get('user.id'),
        ]);
        */
        return $res->withRedirect($this->helper->completePathFor(
            'proPictureGet',
            ['pro' => $project->id]
        ));
    }

    public function viewSetImageProject($req, $res, $arg)
    {
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

    public function setImageProject($req, $res, $arg)
    {
        $project = $this->db->query('App:Project')->findOrFail($arg['pro']);
        $user = $project->user;
        //TODO ver si el control de acceso se hace mas lindo
        if ($user->id != $this->session->get('user.id')) {
            throw new \App\Util\AppException('Forbiden.', 405);
        }
        
        $files = $req->getUploadedFiles();
        if (empty($files['imagen'])) {
            throw new \App\Util\AppException('No se envió ninguna imagen.', 400);
        }
        $imgFile = $files['imagen'];
        if ($imgFile->getError() !== UPLOAD_ERR_OK) {
            throw new \App\Util\AppException('Hubo un error con la imagen recibida', 400);
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

        return $res->withRedirect(
            $this->helper->completePathFor('proPictureGet', ['pro' => $project->id])
        );
    }
}
