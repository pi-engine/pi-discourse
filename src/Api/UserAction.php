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
//        return $this->getUserActionCount($user_id);
        $userData = Pi::service('api')->discourse(array('user', 'getCurrentUserInfo'));
        d($userData);
        switch ($filter) {
            case static::ACTION_FILTER_BOOKMARK:
                $userActionModel    = \Pi::model('user_action', 'discourse');
                $select = $userActionModel->select()
                    ->where(array(
                        'user_id' => $userData[id],
                        'action_type' => static::USER_ACTION_BOOKMARK,
                    ))
                    ->offset(intval(($page - 1) * static::PER_PAGE))
                    ->limit(intval(static::PER_PAGE));
                $rowset = $userActionModel->selectWith($select);
                $result = $rowset->toArray();
                return $result;
//                
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
            case static::ACTION_FILTER_LIKE_GIVEN:
                $userActionModel    = \Pi::model('user_action', 'discourse');
                $select = $userActionModel->select()
                    ->where(array(
                        'user_id' => $userData[id],
                        'action_type' => static::USER_ACTION_LIKE,
                    ))
                    ->offset(intval(($page - 1) * static::PER_PAGE))
                    ->limit(intval(static::PER_PAGE));
                $rowset = $userActionModel->selectWith($select);
                $result = $rowset->toArray();
                return $result;
                break;
            case static::ACTION_FILTER_LIKE_RECEIVE:
                $userActionModel    = \Pi::model('user_action', 'discourse');
                $select = $userActionModel->select()
                    ->where(array(
                        'target_user_id' => $userData[id],
                        'action_type' => static::USER_ACTION_LIKE,
                    ))
                    ->offset(intval(($page - 1) * static::PER_PAGE))
                    ->limit(intval(static::PER_PAGE));
                $rowset = $userActionModel->selectWith($select);
                $result = $rowset->toArray();
                return $result;
                break;
            case static::ACTION_FILTER_STAR:
                $userActionModel    = \Pi::model('user_action', 'discourse');
                $select = $userActionModel->select()
                    ->where(array(
                        'user_id' => $userData[id],
                        'action_type' => static::USER_ACTION_STAR,
                    ))
                    ->offset(intval(($page - 1) * static::PER_PAGE))
                    ->limit(intval(static::PER_PAGE));
                $rowset = $userActionModel->selectWith($select);
                $result = $rowset->toArray();
                return $result;
                break;
            case static::ACTION_FILTER_TOPIC:
                
                break;
            case static::ACTION_FILTER_POST:
                
                break;
            case static::ACTION_FILTER_RESPONSE:
                $userActionModel    = \Pi::model('user_action', 'discourse');
                $select = $userActionModel->select()
                    ->where(array(
                        'user_id' => $userData[id],
                        'action_type' => static::USER_ACTION_RESPONSE,
                    ))
                    ->offset(intval(($page - 1) * static::PER_PAGE))
                    ->limit(intval(static::PER_PAGE));
                $rowset = $userActionModel->selectWith($select);
                $result = $rowset->toArray();
                return $result;
                break;
        }
    }
    
    function getUserActionCount($user_id)
    {
        $userData = Pi::service('api')->discourse(array('user', 'getCurrentUserInfo'));
        $userActionModel = Pi::model('user_action', 'discourse');
        $count = array();
        
        $select = $userActionModel->select()->columns(array(
                    'action_type' => 'action_type',
                    'count' => new \Zend\Db\Sql\Expression('count(*)'),
                ))
                ->where(array(
                    'user_id' => $userData[id],
                ))
                ->group('action_type');
        
        $rowset = $userActionModel->selectWith($select);
        $result = $rowset->toArray();
        foreach ($result as &$data){
            switch ($data['action_type']) {
                case static::USER_ACTION_BOOKMARK:
                    $count['bookmark'] = $data['count'];
                case static::USER_ACTION_LIKE:
                    $count['like'] = $data['count'];
                case static::USER_ACTION_STAR:
                    $count['star'] = $data['count'];
                case static::USER_ACTION_RESPONSE:
                    $count['response'] = $data['count'];
            }
        }
        
        $select = $userActionModel->select()->columns(array(
                    'count' => new \Zend\Db\Sql\Expression('count(*)'),
                ))
                ->where(array(
                    'target_user_id'    => $userData[id],
                    'action_type'       => static::USER_ACTION_LIKE,
                ));
        
        $likeReceivrCountSet = $userActionModel->selectWith($select);
        $likeReceivrCount = $likeReceivrCountSet->current()->toArray();
        $count['like_receive'] = $likeReceivrCount['count'];
        
        $topicModel = Pi::model('topic', 'discourse');
        $select = $topicModel->select()->columns(array(
                    'count' => new \Zend\Db\Sql\Expression('count(*)'),
                ))
                ->where(array(
                    'user_id' => $userData[id],
                ));
        $topicCountSet = $topicModel->selectWith($select);
        $topicCount = $topicCountSet->current()->toArray();
        $count['topic'] = $topicCount['count'];
        
        $postModel = Pi::model('post', 'discourse');
        $select = $postModel->select()->columns(array(
                    'count' => new \Zend\Db\Sql\Expression('count(*)'),
                ))
                ->where(array(
                    'user_id' => $userData[id],
                ));
        $postCountSet = $postModel->selectWith($select);
        $postCount = $postCountSet->current()->toArray();
        $count['post'] = $postCount['count'];
        
        return $count;
    }
}