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
        
        if ($this->userInfo['id']) {
            Pi::service('api')->discourse(array('notification', 'markNotificationReadByTopic'), $this->userInfo['id'], $id);
            $this->notificationCount = Pi::service('api')->discourse(array('notification', 'getUnreadCount'), $this->userInfo['id']);
            $this->preStore('notificationCount', $this->notificationCount);
        }
        
        $topic          = Pi::service('api')->discourse(array('topic', 'getTopic'), $id);
        $postsAndUsers  = Pi::service('api')->discourse(array('post', 'getPosts'), $id);
        
        $this->preStore('topic', $topic);
        $this->preStore('postsAndUsers', $postsAndUsers);
        
        $this->view()->assign('controllerName', str_replace('Action', '', __FUNCTION__));
        $this->view()->assign('preloadStore', $this->preloadStore);
        
        $this->view()->setLayout('layout-content');
        $this->view()->setTemplate('discourse');
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