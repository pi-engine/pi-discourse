<?php
/* << replace >>*/

namespace Module\Discourse\Controller\Front;

use Pi;
use Module\Discourse\Lib\FrontController;


class ClientUserController extends FrontController
{
    public function userAction()
    {
        $id = $this->params('id');
        $userData = Pi::service('api')->discourse(array('user', 'get'), $id);
        $userActionCountData = Pi::service('api')->discourse(array('userAction', 'getUserActionCount'), $id);
        
        $this->preStore('userData', $userData);
        $this->preStore('userActionCountData', $userActionCountData);
        
        $this->view()->assign('actionName', str_replace('Action', '', __FUNCTION__));
        $this->view()->assign('preloadStore', $this->preloadStore);
        
        $this->view()->setTemplate('user');
        
//        $clres = clone $this->response;
//        $clres->setContent("aaaaa");
//        $clres->send();
        return $clres;
        
    }
    
    public function userJsonAction()
    {
//        $id = $this->params('id');
//        $topic          = Pi::service('api')->discourse(array('topic', 'getTopic'), $id);
//        $postsAndUsers  = Pi::service('api')->discourse(array('post', 'getPosts'), $id);
//
//        return json_encode(array(
//            'topic' => $topic, 
//            'postsAndUsers' => $postsAndUsers
//        ));
    }
}