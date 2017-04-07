<?php namespace App\Action;

final class ProjectAction extends AbstractAction
{
    public function viewProject($req, $res, $arg)
    {
        $project = $this->db->query('App:Project')->findOrFail($arg['pro']);
        return $this->view->render($res, 'proyecto.html.twig', [
            'project' => $project,
        ]);
    }

    public function voteProject($req, $res, $arg)
    {
        $userID = $this->session->get('user.id');
        $project = $this->db->query('App:Project')->findOrFail($arg['pro']);
        /*
        $project = $this->db->query('App:Project')->whereDoesntHave('voters', function ($q) {
            $q->where('id', $userID);
        })->where('id', $arg['pro'])->firstOrFail();
        */
        $project->voters()->toggle($userID);
        $project->likes = $project->voters()->count();
        $project->save();

        return $res->withJSON([
            'mensaje' => '¡Proyecto bancado!',
        ]);
    }

    public function commentProject($req, $res, $arg)
    {
        $userID = $this->session->get('user.id');
        $project = $this->db->query('App:Project')->findOrFail($arg['pro']);

        $params = $req->getParsedBody();
        if (!$this->validator['comment']->validate($params)) {
            throw new \App\Util\AppException('Parametros invalidos', 400);
        }

        $comment = new \App\Model\Comment();
        $comment->user_id = $userID;
        $comment->project_id = $project->id;
        $comment->content = $params['content'];
        $comment->save();

        return $res->withJSON([
            'mensaje' => 'Comentario realizado.',
        ]);
    }

    public function replyComment($req, $res, $arg)
    {
        $userID = $this->session->get('user.id');
        $parent = $this->db->query('App:Comment')->findOrFail($arg['com']);

        $params = $req->getParsedBody();
        if (!$this->validator['comment']->validate($params)) {
            throw new \App\Util\AppException('Parametros invalidos', 400);
        }

        $comment = new \App\Model\Comment();
        $comment->user_id = $userID;
        $comment->parent_id = $parent->id;
        $comment->content = $params['content'];
        $comment->save();

        return $res->withJSON([
            'mensaje' => 'Comentario respondido.',
        ]);
    }

    public function rateComment($req, $res, $arg)
    {
        $userID = $this->session->get('user.id');
        $comment = $this->db->query('App:Comment')->findOrFail($arg['com']);

        $params = $req->getParsedBody();
        if (!$this->validator['rate']->validate($params)) {
            throw new \App\Util\AppException('Parametros invalidos.', 400);
        }

        $comment->raters()->updateExistingPivot($userID, ['value' => $params['value']]);
        $comment->votes = $comment->raters->sum('pivot.value');
        $comment->save();
        
        /*
        if ($comment->raters->contains($userID)) {
            $comment->raters()->updateExistingPivot($userID, ['value' => $params['value']]);
        } else {
            $evento->usuarios()->detach($usuario->id);
        }
        */

        return $res->withJSON([
            'mensaje' => 'Comentario votado.',
        ]);
    }

    private function listProjects($params = array())
    {
        $query = $this->db->query('App:Project');
        if (isset($params['categoria'])) {
            $query = $query->where('categoria', $params['categoria']);
        }
        if (isset($params['s'])) {
            $filter = $this->helper->generateTrace($params['q']);
            $query = $query
                ->where('name_trace', 'LIKE', "%$filter%")
                ->orWhere('group_trace', 'LIKE', "%$filter%");
        }
        if (isset($params['sort'])) {
            $sorter = $params['sort'];
            $direction = 'ASC';
            if (in_array($sorter, ['likes', 'created_at'])) {
                if (substr($sorter, 0, 1) == '-') {
                    $direction = 'DESC';
                    $sorter = substr($sorter, 1);
                }
                $query = $query->orderBy($sorter, $direction);
            }
        }
        $url = $this->helper->currentUrl();
        $paginator = new App\Util\Paginator($query, $url, $params);
        return $res
            ->withHeader('Link', $paginator->getLinkHeader())
            ->withJSON($paginator->rows->toArray());
    }
}
