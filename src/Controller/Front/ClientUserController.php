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
        
        $this->view()->assign('controllerName', str_replace('Action', '', __FUNCTION__));
        $this->view()->assign('preloadStore', $this->preloadStore);
        
        $this->view()->setTemplate('user');

        return $clres;
        
    }
    
    public function userJsonAction()
    {
        $id = $this->params('id');
        $userData = Pi::service('api')->discourse(array('user', 'get'), $id);
        $userActionCountData = Pi::service('api')->discourse(array('userAction', 'getUserActionCount'), $id);
        
        return json_encode(array(
            'userData'              => $userData,
            'userActionCountData'   => $userActionCountData
        ));
    }
}