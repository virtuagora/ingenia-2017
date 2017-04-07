<?php
namespace App\Util;

class TwigExtension extends \Twig_Extension
{
    private $helper;
    private $csrf;
    private $facebook;
    private $session;
    
    public function __construct($helper, $csrf, $facebook, $session)
    {
        $this->helper = $helper;
        $this->csrf = $csrf;
        $this->facebook = $facebook;
        $this->session = $session;
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
            'user' => $this->session->get('user', null),
        ];
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('path_for', array($this, 'pathFor')),
            new \Twig_SimpleFunction('base_url', array($this, 'baseUrl')),
            new \Twig_SimpleFunction('is_current_path', array($this, 'isCurrentPath')),
            new \Twig_SimpleFunction('asset', array($this, 'asset')),
            new \Twig_SimpleFunction('category_name', array($this, 'categoryName')),
            new \Twig_SimpleFunction('facebook_login_link', array($this, 'facebookLoginLink')),
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

    public function categoryName($category){
        if($category == 'social'){
            return 'Integración Social';
        }else if($category == 'educacion'){
            return 'Educación';
        }else if($category == 'comunicacion'){
            return 'Comunicación';
        }else if($category == 'ambiente'){
            return 'Medio Ambiente';
        }else if($category == 'cultura'){
            return 'Cultura';
        }else if($category == 'salud'){
            return 'Salud';
        }else if($category == 'deporte'){
            return 'Deporte y recreación';
        }else if($category == 'empleo'){
            return 'Empleo y Capacitación';
        }
        return 'undefined';
    }

    public function facebookLoginLink() {
        $helper = $this->facebook->getRedirectLoginHelper();
        $permissions = ['email'];
        return $helper->getLoginUrl(
            $this->helper->completePathFor('fbCallbackGet'), $permissions
        );
    }
}
