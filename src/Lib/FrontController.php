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
    
    public $notificationCount;
    
    public function __construct() {
        $this->preStoreData();
    }
    
    public function preStore($key, $data)
    {
        $this->preloadStore .= 
                "PreloadStore.store(" 
                . "\"" . $key . "\","
                . json_encode($data) . ");";
    }
    
    public function preStoreData()
    {
        $this->categories   = Pi::service('api')->discourse(array('category', 'allCategories'));
        $this->userInfo     = Pi::service('api')->discourse(array('user', 'getCurrentUserInfo'));
        if ($this->userInfo['id']) {
            $this->notificationCount = Pi::service('api')->discourse(array('notification', 'getUnreadCount'), $this->userInfo['id']);
            $this->preStore('notificationCount', $this->notificationCount);
//            Pi::service('api')->discourse(array('notification', 'getUnreadNotification'), $this->userInfo['id']);
        }
        
        $this->preStore('user', $this->userInfo);
        $this->preStore('categories', $this->categories);
    }
}
