<?php
/* << replace >>*/

namespace Module\Discourse\Lib;

use \Pi;
use \Pi\Mvc\Controller\ActionController;
use Module\Discourse\Controller\Front\CategoryController;
use Module\Discourse\Controller\Front\UserController;
use Module\Discourse\Controller\Front\TopicController;

class FrontController extends ActionController
{
    public $preloadStore = '';

    public $categories;
    
    public $userInfo;
    
    // category controller
    public $cc;
    
    // user controller 
    public $uc;
    
    // topic controller
    public $tc;
    
    public function __construct() {
        $this->preStoreData();
        Pi::service('theme')->setTheme('discourse');
    }
    
    public function preStore($offset, $data)
    {
        $this->preloadStore .= 
                "PreloadStore.store(" 
                . "\"" . $offset . "\"," 
                . json_encode($data) . ");";
    }
    
    public function preStoreData()
    {
        $this->categories = $this->cc()->allCategories();
        
        $this->userInfo = $this->uc()->getCurrentUserInfo();
                
        $this->preStore('user', $this->userInfo);
        $this->preStore('categories', $this->categories);
    }
    
    public function uc()
    {
        if (!isset($this->uc)) {
            $this->uc = new UserController();
        }
        return $this->uc;
    }
    
    public function cc()
    {
        if (!isset($this->cc)) {
            $this->cc = new CategoryController();
        }
        return $this->cc;
    }
    
    public function tc()
    {
        if (!isset($this->tc)) {
            $this->tc = new TopicController();
        }
        return $this->tc;
    }
}
