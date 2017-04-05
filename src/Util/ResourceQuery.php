<?php

namespace App\Util;

class ResourceMaker
{
    public $query;
    public $params;

    public function __construct($query, $params = array())
    {
        $this->params = $params;
        $this->query = $query;
    }

    public function addFilters($filtrables = array(), $searchable = false)
    {
        if (isset($this->params['categoria'])) {
            $this->query = $this->query->where('categoria', $this->params['categoria']);
        }
        if (isset($this->params['s'])) {
            //TODO huella
            $filter = FilterFactory::calcHuella($this->params['q']);
            $this->query = $this->query
                ->where('name_trace', 'LIKE', "%$filter%")
                ->orWhere('group_trace', 'LIKE', "%$filter%");
        }
        if (isset($this->params['sort'])) {
            $sorter = $this->params['sort'];
            $direction = 'ASC';
            if (in_array($sorter, ['likes', 'created_at'])) {
                if (substr($sorter, 0, 1) == '-') {
                    $direction = 'DESC';
                    $sorter = substr($sorter, 1);
                }
                $this->query = $this->query->orderBy($sorter, $direction);
            }
        }
    }
}