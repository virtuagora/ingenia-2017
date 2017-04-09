<?php namespace App\Action;

final class HomeAction extends AbstractAction
{
    public function viewHome($req, $res, $arg)
    {
        return $this->view->render($res, 'home.html.twig');
    }

    public function newProject($req, $res, $arg)
    {
        $subId = $arg['sub-id'];
        $subBody = $this->jotform->getSubmission($subId);
        //TODO cambiar al form de ingenia
        if ($subBody['form_id'] != '70946170891665') {
            throw new \App\Util\AppException('Bad request.', 400);
        }
        //$this->logger->info(json_encode($subBody));
        $formData = [
            'jotform' => $subId,
            'group' => $subBody['answers']['4']['answer'],
            'name' => $subBody['answers']['81']['answer'],
            'description' => $subBody['answers']['82']['answer'],
            'foundation' => $subBody['answers']['83']['answer'],
            'execution' => isset($subBody['answers']['86'])?
                $subBody['answers']['86']['answer']: null,
            'organization' => isset($subBody['answers']['89'])?
                $subBody['answers']['89']['answer']: null,
            'schedule' => $subBody['answers']['96']['answer'],
            'budget' => $subBody['answers']['98']['answer'],
            'total_budget' => $subBody['answers']['99']['answer'],
            'place' => trim($subBody['answers']['179']['answer']),
            'category' => $subBody['answers']['181']['answer'],
        ];
        //$this->logger->info(json_encode($formData));
        $this->session->set('project', $formData);
        if ($this->session->has('user')) {
            return $this->view->render($res, 'comienzo-logueado.html.twig');
        } else {
            return $this->view->render($res, 'comienzo.html.twig');
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
        if (!$this->session->has('user')) {
            throw new \App\Util\AppException('Necesitás identificarte para realizar esta acción.', 403);
        }
        if (!$this->session->has('project')) {
            throw new \App\Util\AppException('Petición inválida.', 400);
        }
        $project = $this->db->query('App:Project')->where('jotform', $this->session->get('project'))->first();
        if ($project) {
            throw new \App\Util\AppException('El proyecto ya está registrado.', 400);
        }
        //$this->logger->info(json_encode($this->session->get('project')));
        $categoryList = array_flip($this->helper->getCategories());
        $project = new \App\Model\Project();
        $project->name = $this->session->get('project.name');
        $project->jotform = $this->session->get('project.jotform');
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
        $budget = [];
        $rawBudget = json_decode($this->session->get('project.budget'), true);
        foreach($rawBudget as $item) {
            $values = array_values($item);
            $budget[] = [
                'rubro' => $values[0],
                'descripcion' => $values[1],
                'monto' => $values[2],
            ];
        }
        $project->budget = $budget;
        $schedule = [];
        $rawSchedule = json_decode($this->session->get('project.schedule'), true);
        foreach($rawSchedule as $item) {
            $values = array_values($item);
            $schedule[] = [
                'dia' => $values[0].'/'.$values[1],
                'actividad' => $values[2],
            ];
        }
        $project->schedule = $schedule;
        $project->name_trace = $this->helper->generateTrace($project->name);
        $project->group_trace = $this->helper->generateTrace($project->group);
        $project->user_id = $this->session->get('user.id');
        $project->save();
        $this->jotform->editSubmission($project->jotform, ['348' => 'SI']);
        return $res->withRedirect($this->helper->completePathFor(
            'proSetImgGet',
            ['pro' => $project->id]
        ));
    }

    public function viewSetImageProject($req, $res, $arg)
    {
        if (!$this->session->has('user')) {
            throw new \App\Util\AppException('Necesitás identificarte para realizar esta acción.', 403);
        }
        $project = $this->db->query('App:Project')->findOrFail($arg['pro']);
        $user = $project->user;
        if ($user->id != $this->session->get('user.id')) {
            throw new \App\Util\AppException('Acceso denegado.', 403);
        }
        return $this->view->render($res, 'upload.html.twig', [
            'project' => $project,
        ]);
    }

    public function setImageProject($req, $res, $arg)
    {
        if (!$this->session->has('user')) {
            throw new \App\Util\AppException('Necesitás identificarte para realizar esta acción.', 403);
        }
        $project = $this->db->query('App:Project')->findOrFail($arg['pro']);
        $user = $project->user;
        if ($user->id != $this->session->get('user.id')) {
            throw new \App\Util\AppException('Acceso denegado.', 405);
        }
        $files = $req->getUploadedFiles();
        if (empty($files['imagen'])) {
            throw new \App\Util\AppException('No se envió ninguna imagen.', 400);
        }
        $imgFile = $files['imagen'];
        if ($imgFile->getError() !== UPLOAD_ERR_OK) {
            throw new \App\Util\AppException('Hubo un error con la imagen recibida', 400);
        }
        $imgStrm = $this->image->make($imgFile->getStream()->detach())
            ->fit(800, 565, function ($constraint) {
                $constraint->upsize();
            })->encode('jpg', 75);
        $this->filesystem->put('project/'.$project->id.'.jpg', $imgStrm);
        if (is_resource($imgStrm)) {
            fclose($imgStrm);
        }
        if (!$project->has_image) {
            $project->has_image = true;
            $project->save();
        }
        return $res->withRedirect(
            $this->helper->completePathFor('proSetImgGet', ['pro' => $project->id])
        );
    }
}
