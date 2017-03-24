<?php namespace App\Service;

class HelperService
{
    private $router;
    private $request;
    
    public function __construct($router, $request)
    {
        $this->router = $router;
        $this->request = $request;
    }

    public function baseUrl()
    {
        return $this->request->getUri()->getBaseUrl();
    }

    public function pathFor($name, $params = [], $query = [])
    {
        return $this->router->pathFor($name, $params, $query);
    }

    public function completePathFor($name, $params = [], $query = [])
    {
        return $this->baseUrl().$this->pathFor($name, $params, $query);
    }
}
