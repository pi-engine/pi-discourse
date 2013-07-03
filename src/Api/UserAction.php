<?php
/* << replace >>*/

namespace Module\Discourse\Api;

use Pi;
use Pi\Application\AbstractApi;
use Pi\Db\RowGateway\RowGateway;

class UserAction extends AbstractApi
{
    const USER_ACTION_BOOKMARK      = 1;
    
    const USER_ACTION_LIKE          = 2;
    
    const USER_ACTION_STAR          = 3;
    
    const USER_ACTION_RESPONSE      = 4;
        
    const POST_ACTION_BOOKMARK      = 1;
    
    const POST_ACTION_LIKE          = 2;
    
    const PER_PAGE                  = 20;
    
    /**
     * used for judge which kind of action to response
     */
    const ACTION_FILTER_BOOKMARK    = 1;
    
    const ACTION_FILTER_LIKE_GIVEN  = 2;
    
    const ACTION_FILTER_LIKE_RECEIVE = 3;
    
    const ACTION_FILTER_STAR        = 4;
    
    const ACTION_FILTER_TOPIC       = 5;
    
    const ACTION_FILTER_POST        = 6;
    
    const ACTION_FILTER_RESPONSE    = 7;

    function getUserAction($user_id, $filter, $page)
    {
        $userData = Pi::service('api')->discourse(array('user', 'getCurrentUserInfo'));
        d(array($user_id, $filter, $page));
        switch ($filter) {
            case static::USER_ACTION_BOOKMARK:
//                $postModel          = \Pi::model('post', 'discourse');
//                $topicModel         = \Pi::model('topic', 'discourse');
//                $userModel          = \Pi::model('user', 'discourse');
//                $postActionModel    = \Pi::model('post_action', 'discourse');
//                
//                $postTable          = $postModel->getTable();
//                $topicTable         = $topicModel->getTable();
//                $userTable          = $userModel->getTable();
//                $postActionTable    = $postActionModel->getTable();
//                
//                $select = $postActionModel->select()
//                    ->columns(array('post_id', 'time_created'))
//                    ->where(array(
//                        $postActionTable . '.user_id' => intval($user_id),
//                        'post_action_type_id'   => static::POST_ACTION_BOOKMARK,
//                    ))
//                    ->join(array('postTable' => $postTable),
//                        'postTable.id = ' . $postActionTable . '.post_id', 
//                        array(
//                            'topic_id',
//                            'post_number',
//                            'raw',
//                        )
//                    )
//                    ->join(array('topicTable' => $topicTable),
//                        'topicTable.id = postTable.topic_id', 
//                        array(
//                            'topic_id' => 'id',
//                            'title',
//                        )
//                    )
//                    ->join(array('userTable' => $userTable),
//                        'userTable.id = postTable.user_id', 
//                        array(
//                            'user_id' => 'id',
//                            'name',
//                            'avatar',
//                        )
//                    )
//                    ->offset(intval(($page - 1) * static::PER_PAGE))
//                    ->limit(intval(static::PER_PAGE));
//                
//                $rowset = $postActionModel->selectWith($select);
//                $result = $rowset->toArray();
//                return $result;
                break;
            case static::USER_ACTION_LIKE_GIVEN:
//                $postModel          = \Pi::model('post', 'discourse');
//                $topicModel         = \Pi::model('topic', 'discourse');
//                $userModel          = \Pi::model('user', 'discourse');
//                $postActionModel    = \Pi::model('post_action', 'discourse');
//                
//                $postTable          = $postModel->getTable();
//                $topicTable         = $topicModel->getTable();
//                $userTable          = $userModel->getTable();
//                $postActionTable    = $postActionModel->getTable();
//                
//                $select = $postActionModel->select()
//                    ->columns(array('post_id', 'time_created'))
//                    ->where(array(
//                        $postActionTable . '.user_id' => intval($user_id),
//                        'post_action_type_id'   => static::POST_ACTION_LIKE,
//                    ))
//                    ->join(array('postTable' => $postTable),
//                        'postTable.id = ' . $postActionTable . '.post_id', 
//                        array(
//                            'topic_id',
//                            'post_number',
//                            'raw',
//                        )
//                    )
//                    ->join(array('topicTable' => $topicTable),
//                        'topicTable.id = postTable.topic_id', 
//                        array(
//                            'topic_id' => 'id',
//                            'title',
//                        )
//                    )
//                    ->join(array('userTable' => $userTable),
//                        'userTable.id = postTable.user_id', 
//                        array(
//                            'user_id' => 'id',
//                            'name',
//                            'avatar',
//                        )
//                    )
//                    ->offset(intval(($page - 1) * static::PER_PAGE))
//                    ->limit(intval(static::PER_PAGE));
//                
//                $rowset = $postActionModel->selectWith($select);
//                $result = $rowset->toArray();
//                return $result;
                break;
            case static::USER_ACTION_LIKE_RECEIVE:
                
                break;
        }
    }
}