<?php
/* << replace >>*/

namespace Module\Discourse\Controller\Front;

use Pi;
use Module\Discourse\Lib\DiscourseRestfulController;
use Module\Discourse\Controller\Front\UserController as UC;

/**
 * 
 * For temporary test, see scripts in:
 * Browser Console Tests: https://github.com/pi-engine/pi-discourse/wiki/Browser-Console-Tests
 * 
 */

class StarController extends DiscourseRestfulController
{
    public $uc;
    
    public $userData;
    
    public function __construct() {
        $this->uc = new UC();
        $this->userData = $this->uc->getCurrentUserInfo();
    }
    
    /**
     * /star/ GET
     * 
     */
    public function get($id)
    {
        throw new \Zend\Mvc\Exception\DomainException('Invalid HTTP method!');
    }
    
    /**
     * /star/ POST
     * 
     */   
    public function create($data)
    {
        throw new \Zend\Mvc\Exception\DomainException('Invalid HTTP method!');
    }
    
    /**
     * /star/{id} PUT
     * 
     */
    public function update($id, $parsedParams)
    {
        return json_encode($this->handle($id, $parsedParams));
//        throw new \Zend\Mvc\Exception\DomainException('Invalid HTTP method!');
    }
    
    /**
     * /star/{id} DELETE
     * 
     */    
    public function delete($id)
    {
        throw new \Zend\Mvc\Exception\DomainException('Invalid HTTP method!');
    }
    
    /**
     * /star/{id}/{num1}/{num2} GET
     * 
     * 
     */
    public function getMulti($postId, $postActionType = 1, $limit = 20)
    {
        return json_encode($postId);
//        throw new \Zend\Mvc\Exception\DomainException('Invalid HTTP method!');
    }
    
    public function handle($topicId, $parsedParams)
    {
        $topicModel     = \Pi::model('topic', 'discourse');
        $topicUserModel = \Pi::model('topic_user', 'discourse');
        
        $userData = $this->userData;
        if($userData['isguest']) {
            return array( 'err_msg' => "You haven't logged in." );
        }
        
        if(isset($topicId)){
            $topicRow = $topicModel->find(intval($topicId));
            if(!$topicRow->id) {
                return array( 'err_msg' => "No such topic." );
            }
        } else {
            return array( 'err_msg' => "Require topic id." );
        }
                
        $select = $topicUserModel->select()
                    ->where(array('topic_id' => $topicId, 'user_id' => $userData['id'] ));
        $topicUserRowset = $topicUserModel->selectWith($select);
        $topicUserRow = $topicUserRowset->toArray();
        
        if(!$topicUserRow[0]) {
            $topicUserData = array(
                                'topic_id'      => $topicId,
                                'user_id'       => $userData['id'],
                                'starred'       => (bool)$parsedParams['starred'],
                                'time_starred'  => time(),
                            );
            
            $topicUserRow = $topicUserModel->createRow($topicUserData);
            $topicUserRow->save();
            return array(
                        'topic_id'  => (int)$topicId, 
                        'starred'   => (int)$parsedParams['starred']
                    );
        } else {
            $topicUserData = array(
                                'starred'       => (bool)$parsedParams['starred'],
                                'time_starred'  => time(),
                            );
            $topicUserModel->update($topicUserData,
                                    array(
                                        'topic_id'  => $topicId,
                                        'user_id'   => $userData['id'],
                                    )
                                );
            return array(
                        'topic_id'  => (int)$topicId, 
                        'starred'   => (int)$parsedParams['starred']
                    );
        }
    }
}