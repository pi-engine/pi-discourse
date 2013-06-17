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
//        $this->categories = $this->cc()->allCategories();
//        $this->userInfo = $this->uc()->getCurrentUserInfo();
        $this->categories   = Pi::service('api')->discourse(array('category', 'allCategories'));
        $this->userInfo     = Pi::service('api')->discourse(array('user', 'getCurrentUserInfo'));
  
        $this->preStore('user', $this->userInfo);
        $this->preStore('categories', $this->categories);
    }
}
