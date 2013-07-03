<?php
/* << replace >>*/

namespace Module\Discourse\Controller\Front;

use Pi;
use Module\Discourse\Lib\FrontController;


class ClientTopicController extends FrontController
{
    public function topicAction()
    {
        $id = $this->params('id');
        $topic          = Pi::service('api')->discourse(array('topic', 'getTopic'), $id);
        $postsAndUsers  = Pi::service('api')->discourse(array('post', 'getPosts'), $id);

        $this->preStore('topic', $topic);
        $this->preStore('postsAndUsers', $postsAndUsers);
        
        $this->view()->assign('actionName', str_replace('Action', '', __FUNCTION__));
        $this->view()->assign('preloadStore', $this->preloadStore);
        
        $this->view()->setTemplate('topic');
    }
    
    public function topicJsonAction()
    {
        $id = $this->params('id');
        $topic          = Pi::service('api')->discourse(array('topic', 'getTopic'), $id);
        $postsAndUsers  = Pi::service('api')->discourse(array('post', 'getPosts'), $id);

        return json_encode(array(
            'topic' => $topic, 
            'postsAndUsers' => $postsAndUsers
        ));
    }
}