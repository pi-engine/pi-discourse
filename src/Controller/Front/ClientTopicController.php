<?php
/* << replace >>*/

namespace Module\Discourse\Controller\Front;

use Module\Discourse\Lib\FrontController;


class ClientTopicController extends FrontController
{
    public function topicAction()
    {
        $id = $this->params('id');
        $topic = $this->tc()->getTopic($id);
        $postsAndUsers = $this->tc()->getPosts($id);
        $this->preStore('topic', $topic);
        $this->preStore('postsAndUsers', $postsAndUsers);
        
        $this->view()->assign('actionName', str_replace('Action', '', __FUNCTION__));
        $this->view()->assign('preloadStore', $this->preloadStore);
        
        $this->view()->setTemplate('topic');
    }
    
    public function topicAjax()
    {
        
    }
}