<?php
/* << replace >>*/

namespace Module\Discourse\Controller\Front;

use Module\Discourse\Lib\FrontController;
//use Module\Discourse\Controller\Front\CategoryController as CC;

class ClientCategoryController extends FrontController
{
    public function categoryListAction()
    {
        $topics = $this->cc()->allTopTopics($this->categories);
        $this->preStore('categoryTopics', $topics);
        
        $this->view()->assign('actionName', str_replace('Action', '', __FUNCTION__));
        $this->view()->assign('preloadStore', $this->preloadStore);
        $this->view()->setTemplate('category-list');
    }
    
    public function categoryAction()
    {
        $id = $this->params('id');
        $topics = $this->cc()->getTopics($id);
        $this->preStore('topics', $topics);
        
        $this->view()->assign('actionName', str_replace('Action', '', __FUNCTION__));
        $this->view()->assign('preloadStore', $this->preloadStore);
        $this->view()->setTemplate('category');   
    }
    
    
}