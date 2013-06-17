<?php
/* << replace >>*/

namespace Module\Discourse\Controller\Front;

use Pi;
use Module\Discourse\Lib\DiscourseRestfulController;

/**
 * 
 * For temporary test, see scripts in:
 * Browser Console Tests: https://github.com/pi-engine/pi-discourse/wiki/Browser-Console-Tests
 * 
 */

class PostActionController extends DiscourseRestfulController
{
    /**
     * /operation/ GET
     * 
     */
    public function get($id)
    {
        throw new \Zend\Mvc\Exception\DomainException('Invalid HTTP method!');
    }
    
    /**
     * /operation/ POST
     * 
     */   
    public function create($data)
    {
        return json_encode($this->handleAction($data));
    }
    
    /**
     * /operation/{id} PUT
     * 
     */
    public function update($id, $parsedParams)
    {
        throw new \Zend\Mvc\Exception\DomainException('Invalid HTTP method!');
    }
    
    /**
     * /operation/{id} DELETE
     * 
     */    
    public function delete($id)
    {
        throw new \Zend\Mvc\Exception\DomainException('Invalid HTTP method!');
    }
    
    /**
     * /operation/{id}/{num1}/{num2} GET
     * 
     * here {num1} isn't $offset anymore, used as $postActionType instead.
     * 
     */
    public function getMulti($postId, $postActionType = 1, $limit = 20)
    {
        return json_encode($postId);
//        throw new \Zend\Mvc\Exception\DomainException('Invalid HTTP method!');
    }
    
    
    public function handleAction($data)
    {
        $postModel          = \Pi::model('post', 'discourse');
//        $topicModel         = \Pi::model('topic', 'discourse');
        $postActionModel    = \Pi::model('post_action', 'discourse');
        
        $uc = new UC();
        $userData = $uc->getCurrentUserInfo();
        if($userData['isguest']) {
            return array( 'err_msg' => "You haven't logged in." );
        }
        
        if($data['post_id']) {
            $postRow = $postModel->find(intval($data['post_id']));
            if(!$postRow->id) {
                return array( 'err_msg' => "No such post." );
            } else if(!$data['post_action_type_id']) {
                return array( 'err_msg' => "Require post action type id." );
            }
        } else {
            return array( 'err_msg' => "Require post id." );
        }
        
        $select = $postActionModel->select()
                    ->where(array('post_id' => intval($data['post_id']), 'user_id' => $userData['id'] ));
        $postActionRowset = $postActionModel->selectWith($select);
        $postActionRow = $postActionRowset->toArray();
        
        if(!$postActionRow[0]) {
            $postActionData = array(
                        'post_id'               => intval($data['post_id']),
                        'user_id'               => $userData['id'],
                        'post_action_type_id'   => intval($data['post_action_type_id']),
                        'time_updated'          => time(),
                        'time_created'          => time(),
                    );
            $postActionRow = $postActionModel->createRow($postActionData);
            $postActionRow->save();
        } else {
            $postActionData = array(
                        'post_action_type_id'   => intval($data['post_action_type_id']),
                        'time_updated'          => time(),
                    );
            $postActionModel->update($postActionData,
                                    array(
                                        'post_id'   => intval($data['post_id']),
                                        'user_id'   => $userData['id'],
                                    )
                                );
            return true;
        }
        
        return json_encode($data);
    }
}