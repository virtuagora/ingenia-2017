<?php
namespace App\Util;

use Respect\Validation\Validator as v;

class Paginator
{
    public $query;
    public $rows;
    public $links;

    public function __construct($query, $url = '', $params = array())
    {
        $page = isset($params['page'])? $params['page']: 1;
        $take =isset($params['take'])? $params['take']: 10;
        $endless = isset($params['endless']);
        if ($endless) {
            $moreRows = false;
            if (isset($params['explore']) && $params['explore']) {
                $page = 1;
                $moreRows = true;
                $query = $query->whereIn('id', array_unique([
                    rand(7,1064),rand(7,1064),rand(7,1064),rand(7,1064),rand(7,1064),
	                rand(7,1064),rand(7,1064),rand(7,1064),rand(7,1064),rand(7,1064),
                ]));
            }
            $this->query = $query->skip(($page-1)*$take)->take($take+1);
            $this->rows = $this->query->get();
            $moreRows = $moreRows || ($this->rows->count() > $take);
        } else {
            $lastPage = ceil($query->count()/$take);
            $page = min($page, $lastPage);
            $this->query = $query->skip(($page-1)*$take)->take($take);
            $this->rows = $this->query->get();
            $moreRows = ($page < $lastPage);
        }
        $this->links = array();
        if ($moreRows) {
            $params['page'] = $page+1;
            $this->links['next'] = $url . '?' . http_build_query($params);
            if ($endless) {
                $this->rows->pop();
            } else {
                $params['page'] = $lastPage;
                $this->links['last'] = $url . '?' . http_build_query($params);
            }
        }
        if ($page > 1) {
            $params['page'] = $page-1;
            $this->links['prev'] = $url . '?' . http_build_query($params);
            $params['page'] = 1;
            $this->links['first'] = $url . '?' . http_build_query($params);
        }
    }

    public function getLinkHeader()
    {
        $linkArray = array();
        foreach ($this->links as $rel => $link) {
            $linkArray[] = '<' . $link . '>; rel="' . $rel . '"';
        }
        return implode(', ', $linkArray);
    }
}
