<?php
namespace App\Util;

class TwigExtension extends \Twig_Extension implements \Twig_Extension_GlobalsInterface
{
    private $helper;
    private $facebook;
    private $session;
    
    public function __construct($helper, $facebook, $session, $emojione)
    {
        $this->helper = $helper;
        $this->facebook = $facebook;
        $this->session = $session;
        $this->emojione = $emojione;
    }

    public function getName()
    {
        return 'slim';
    }

    public function getGlobals()
    {
        return [
            'csrf' => [
                'key' => 'csrf_token',
                'value' => $this->session->getFrom('csrf', 'token'),
            ],
            'user' => $this->session->get('user'),
        ];
    }

    public function getFilters()
    {
        return [
            new \Twig_Filter('emoji', [$this, 'emoji'], ['pre_escape' => 'html', 'is_safe' => ['html']]),
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
            new \Twig_SimpleFunction('categories', array($this, 'categories')),
            new \Twig_SimpleFunction('places', array($this, 'places')),
        ];
    }

    public function emoji($str)
    {
        return $this->emojione->shortnameToImage($str);
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
        //TODO no implementado
        return true;
    }

    public function asset($name)
    {
        return $this->helper->baseUrl().'/assets/'.$name;
    }

    public function categoryName($category)
    {
        if (isset($this->helper->getCategories()[$category])) {
            return $this->helper->getCategories()[$category];
        } else {
            return 'undefined';
        }
    }

    public function facebookLoginLink() {
        $helper = $this->facebook->getRedirectLoginHelper();
        $permissions = ['email'];
        return $helper->getLoginUrl(
            $this->helper->completePathFor('fbCallbackGet'), $permissions
        );
    }

    public function categories()
    {
        return $this->helper->getCategories();
    }

    public function places()
    {
        return $this->helper->getPlaces();
    }
}
