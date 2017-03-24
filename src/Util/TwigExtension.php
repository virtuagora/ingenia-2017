<?php
namespace App\Util;

class TwigExtension extends \Twig_Extension
{
    private $helper;
    private $csrf;
    
    public function __construct($helper, $csrf)
    {
        $this->helper = $helper;
        $this->csrf = $csrf;
    }

    public function getGlobals()
    {
        return [
            'csrf' => [
                'keys' => [
                    'name' => $this->csrf->getTokenNameKey(),
                    'value' => $this->csrf->getTokenValueKey(),
                ],
                'name' => $this->csrf->getTokenName(),
                'value' => $this->csrf->getTokenValue(),
            ],
        ];
    }

    public function getFunctions()
    {
        return [
            'asset' => new \Twig_Function_Method($this, 'assetFunction'),
            'path' => new \Twig_Function_Method($this, 'pathFunction'),
            'base_url' => new \Twig_Function_Method($this, 'baseUrlFunction'),
        ];
    }
    
    public function assetFunction($name)
    {
        return $this->helper->baseUrl().'/assets/'.$name;
    }
    
    public function pathFunction($name, $params = [], $query = [])
    {
        return $this->helper->pathFor($name, $params, $query);
    }

    public function baseUrlFunction()
    {
         return $this->helper->baseUrl();
    }

    public function getName()
    {
        return 'app';
    }
}
