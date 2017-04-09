<?php namespace App\Action;

final class ProjectAction extends AbstractAction
{
    public function viewProject($req, $res, $arg)
    {
        $userID = $this->session->get('user.id', null);
        $project = $this->db->query('App:Project')->with('comments')->findOrFail($arg['pro']);
        $voted = is_null($userID)? false: $project->voters->contains($userID);
        return $this->view->render($res, 'proyecto.html.twig', [
            'project' => $project,
            'voted' => $voted,
        ]);
    }

    public function voteProject($req, $res, $arg)
    {
        if (!$this->session->has('user')) {
            throw new \App\Util\AppException('Necesitás identificarte para realizar esta acción.', 403);
        }
        $userID = $this->session->get('user.id');
        $project = $this->db->query('App:Project')->findOrFail($arg['pro']);
        /*
        $project = $this->db->query('App:Project')->whereDoesntHave('voters', function ($q) {
            $q->where('id', $userID);
        })->where('id', $arg['pro'])->firstOrFail();
        */
        $result = $project->voters()->toggle($userID);
        $project->likes = $project->voters()->count();
        $project->save();
        $vote = empty($result['detached']);
        return $res->withJSON([
            'mensaje' => $vote? '¡Proyecto bancado!': 'Proyecto ya no bancado.',
            'vote' => $vote,
        ]);
    }

    public function commentProject($req, $res, $arg)
    {
        if (!$this->session->has('user')) {
            throw new \App\Util\AppException('Necesitás identificarte para realizar esta acción.', 403);
        }
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
            'id' => $comment->id,
        ]);
    }

    public function replyComment($req, $res, $arg)
    {
        if (!$this->session->has('user')) {
            throw new \App\Util\AppException('Necesitás identificarte para realizar esta acción.', 403);
        }
        $userID = $this->session->get('user.id');
        $parent = $this->db->query('App:Comment')->findOrFail($arg['com']);
        if ($parent->parent != null) {
            $parent = $parent->parent;
        }
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
            'id' => $comment->id,
        ]);
    }

    public function rateComment($req, $res, $arg)
    {
        if (!$this->session->has('user')) {
            throw new \App\Util\AppException('Necesitás identificarte para realizar esta acción.', 403);
        }
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

    public function listProjects($req, $res, $arg)
    {
        $params = $req->getQueryParams();
        if (!$this->validator['page']->validate($params)) {
            throw new \App\Util\AppException('Parametros invalidos.', 400);
        }
        $query = $this->db->query('App:Project');
        if (isset($params['categoria'])) {
            $query = $query->where('category', $params['categoria']);
        }
        if (isset($params['localidad'])) {
            $query = $query->where('place', $params['localidad']);
        }
        if (isset($params['s'])) {
            $filter = $this->helper->generateTrace($params['s']);
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
        $paginator = new \App\Util\Paginator($query, $url, $params);
        return $res
            ->withHeader('Link', $paginator->getLinkHeader())
            ->withJSON($paginator->rows->toArray());
    }
}
