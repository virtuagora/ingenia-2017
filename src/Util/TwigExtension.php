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

    public function getName()
    {
        return 'slim';
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
            new \Twig_SimpleFunction('path_for', array($this, 'pathFor')),
            new \Twig_SimpleFunction('base_url', array($this, 'baseUrl')),
            new \Twig_SimpleFunction('is_current_path', array($this, 'isCurrentPath')),
            new \Twig_SimpleFunction('asset', array($this, 'asset')),
        ];
    }

    public function pathFor($name, $params = [], $query = [], $appName = 'default')
    {
        return $this->helper->pathFor($name, $params, $query);
    }

    public function baseUrl()
    {
        return $this->helper->baseUrl();
    }

    public function isCurrentPath($name)
    {
        //todo no implementado
        return true;
    }

    public function asset($name)
    {
        return $this->helper->baseUrl().'/assets/'.$name;
    }
}
