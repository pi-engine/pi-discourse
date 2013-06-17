<?php
/* << replace >>*/

namespace Module\Discourse\Api;

use Pi;
use Pi\Application\AbstractApi;
use Pi\Db\RowGateway\RowGateway;

class Star extends AbstractApi
{
    public function handle($topicId, $parsedParams)
    {
        $topicModel     = \Pi::model('topic', 'discourse');
        $topicUserModel = \Pi::model('topic_user', 'discourse');
        
        $userData = Pi::service('api')->discourse(array('user', 'getCurrentUserInfo'));
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