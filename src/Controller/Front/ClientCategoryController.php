<?php
/* << replace >>*/

namespace Module\Discourse\Controller\Front;

use Pi;
use Module\Discourse\Lib\FrontController;

class ClientCategoryController extends FrontController
{
    public function categoryListAction()
    {
        $topics = Pi::service('api')->discourse(array('category', 'allTopTopics'), $this->categories);
        $this->preStore('categoryTopics', $topics);
        
        $this->view()->assign('controllerName', str_replace('Action', '', __FUNCTION__));
        $this->view()->assign('preloadStore', $this->preloadStore);
        $this->view()->setTemplate('category-list');
    }
    
    public function categoryAction()
    {
        $id = $this->params('id');
        $topics = Pi::service('api')->discourse(array('topic', 'getTopics'), $id);
        $this->preStore('topics', $topics);
        
        $this->view()->assign('controllerName', str_replace('Action', '', __FUNCTION__));
        $this->view()->assign('preloadStore', $this->preloadStore);
        $this->view()->setTemplate('category');
    }
    
    public function categoryJsonAction()
    {
        $id = $this->params('id');
        $topics = Pi::service('api')->discourse(array('topic', 'getTopics'), $id);

        return json_encode($topics);
    }
    
    public function categoryListJsonAction()
    {
        $topics = Pi::service('api')->discourse(array('category', 'allTopTopics'), $this->categories);

        return json_encode($topics);
    }
}