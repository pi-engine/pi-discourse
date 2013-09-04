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

    /**
     * wonder if should remove join
     * 
     */
    function getUserAction($user_id, $filter, $page)
    {
//        return $this->getUserActionCount($user_id);
        $userData = Pi::service('api')->discourse(array('user', 'getUserInfo'), $user_id);
        
        switch ($filter) {
            case static::ACTION_FILTER_BOOKMARK:
                $userActionModel    = \Pi::model('user_action', 'discourse');
                $postModel          = \Pi::model('post', 'discourse');
                $topicModel         = \Pi::model('topic', 'discourse');
                $userModel          = \Pi::model('user', 'discourse');
                
                $userActionTable    = $userActionModel->getTable();
                $postTable          = $postModel->getTable();
                $topicTable         = $topicModel->getTable();
                $userTable          = $userModel->getTable();
                
                $select = $userActionModel->select()
                    ->columns(array(
                        'action_user_id'    => 'user_id',
                        'target_post_id'    => 'target_post_id', 
                        'time_created'      => 'time_created',
                    ))
                    ->where(array(
                        $userActionTable . '.user_id' => $userData[id],
                        'action_type' => static::USER_ACTION_BOOKMARK,
                    ))
                    ->join(array('postTable' => $postTable),
                        $userActionTable . '.target_post_id = postTable.id', 
                        array(
                            'topic_id'      => 'topic_id',
                            'post_user_id'  => 'user_id',
                            'post_raw'      => 'raw',
                        ),
                        'left'
                    )
                    ->join(array('topicTable' => $topicTable),
                        'postTable.topic_id = topicTable.id',
                        array(
                            'topic_title'   => 'title',
                            'topic_user_id' => 'user_id',
                        ),
                        'left'
                    )
                    ->join(array('userTable' => $userTable),
                        'topicTable.user_id = userTable.id',
                        array(
                            'topic_user_avatar' => 'avatar',
                            'topic_user_name'   => 'name'
                        ),
                        'left'
                    )
                    ->join(array('userTable2' => $userTable),
                        'postTable.user_id = userTable2.id',
                        array(
                            'post_user_avatar' => 'avatar',
                            'post_user_name'   => 'name'
                        ),
                        'left'
                    )
                    ->offset(intval(($page - 1) * static::PER_PAGE))
                    ->limit(intval(static::PER_PAGE));
                $rowset = $userActionModel->selectWith($select);
                $result = $rowset->toArray();
                foreach ($result as &$row) {
                    $row['action_user_avatar']  = $userData['avatar'];
                    $row['action_user_name']    = $userData['name'];
                    $row['action_type']         = 'bookmark';
                    $row['time_created']        = date('Y-m-d h:i:s', $row['time_created']);
                }
                unset($row);
                d($result);
                return $result;
                break;
                
            case static::ACTION_FILTER_LIKE_GIVEN:
                $userActionModel    = \Pi::model('user_action', 'discourse');
                $postModel          = \Pi::model('post', 'discourse');
                $topicModel         = \Pi::model('topic', 'discourse');
                $userModel          = \Pi::model('user', 'discourse');
                
                $userActionTable    = $userActionModel->getTable();
                $postTable          = $postModel->getTable();
                $topicTable         = $topicModel->getTable();
                $userTable          = $userModel->getTable();
                
                $select = $userActionModel->select()
                    ->columns(array(
                        'action_user_id'    => 'user_id',
                        'target_post_id'    => 'target_post_id', 
                        'time_created'      => 'time_created',
                    ))
                    ->where(array(
                        $userActionTable . '.user_id' => $userData[id],
                        'action_type' => static::USER_ACTION_LIKE,
                    ))
                    ->join(array('postTable' => $postTable),
                        $userActionTable . '.target_post_id = postTable.id', 
                        array(
                            'topic_id'      => 'topic_id',
                            'post_user_id'  => 'user_id',
                            'post_raw'      => 'raw',
                        ),
                        'left'
                    )
                    ->join(array('topicTable' => $topicTable),
                        'postTable.topic_id = topicTable.id',
                        array(
                            'topic_title'   => 'title',
                            'topic_user_id' => 'user_id',
                        ),
                        'left'
                    )
                    ->join(array('userTable' => $userTable),
                        'topicTable.user_id = userTable.id',
                        array(
                            'topic_user_avatar' => 'avatar',
                            'topic_user_name'   => 'name'
                        ),
                        'left'
                    )
                    ->join(array('userTable2' => $userTable),
                        'postTable.user_id = userTable2.id',
                        array(
                            'post_user_avatar' => 'avatar',
                            'post_user_name'   => 'name'
                        ),
                        'left'
                    )
                    ->offset(intval(($page - 1) * static::PER_PAGE))
                    ->limit(intval(static::PER_PAGE));
                $rowset = $userActionModel->selectWith($select);
                $result = $rowset->toArray();
                foreach ($result as &$row) {
                    $row['action_user_avatar']  = $userData['avatar'];
                    $row['action_user_name']    = $userData['name'];
                    $row['action_type']         = 'like';
                    $row['time_created']        = date('Y-m-d h:i:s', $row['time_created']);
                }
                unset($row);
                return $result;
                break;
                
            case static::ACTION_FILTER_LIKE_RECEIVE:
                $userActionModel    = \Pi::model('user_action', 'discourse');
                $postModel          = \Pi::model('post', 'discourse');
                $topicModel         = \Pi::model('topic', 'discourse');
                $userModel          = \Pi::model('user', 'discourse');
                
                $userActionTable    = $userActionModel->getTable();
                $postTable          = $postModel->getTable();
                $topicTable         = $topicModel->getTable();
                $userTable          = $userModel->getTable();
                
                $select = $userActionModel->select()
                    ->columns(array(
                        'action_user_id'    => 'user_id',
                        'target_post_id'    => 'target_post_id', 
                        'time_created'      => 'time_created',
                    ))
                    ->where(array(
                        $userActionTable . '.target_user_id' => $userData[id],
                        'action_type' => static::USER_ACTION_LIKE,
                    ))
                    ->join(array('postTable' => $postTable),
                        $userActionTable . '.target_post_id = postTable.id', 
                        array(
                            'topic_id'      => 'topic_id',
                            'post_user_id'  => 'user_id',
                            'post_raw'      => 'raw',
                        ),
                        'left'
                    )
                    ->join(array('topicTable' => $topicTable),
                        'postTable.topic_id = topicTable.id',
                        array(
                            'topic_title'   => 'title',
                            'topic_user_id' => 'user_id',
                        ),
                        'left'
                    )
                    ->join(array('userTable' => $userTable),
                        'topicTable.user_id = userTable.id',
                        array(
                            'topic_user_avatar' => 'avatar',
                            'topic_user_name'   => 'name'
                        ),
                        'left'
                    )
                    ->join(array('userTable2' => $userTable),
                        'postTable.user_id = userTable2.id',
                        array(
                            'post_user_avatar' => 'avatar',
                            'post_user_name'   => 'name'
                        ),
                        'left'
                    )
                    ->join(array('userTable3' => $userTable),
                        $userActionTable . '.user_id = userTable3.id',
                        array(
                            'action_user_avatar' => 'avatar',
                            'action_user_name'   => 'name'
                        ),
                        'left'
                    )
                    ->offset(intval(($page - 1) * static::PER_PAGE))
                    ->limit(intval(static::PER_PAGE));
                $rowset = $userActionModel->selectWith($select);
                $result = $rowset->toArray();
                foreach ($result as &$row) {
                    $row['action_type']         = 'like';
                    $row['time_created']        = date('Y-m-d h:i:s', $row['time_created']);
                }
                unset($row);
                d($result);
                return $result;
                break;
                
            case static::ACTION_FILTER_STAR:
                $userActionModel    = \Pi::model('user_action', 'discourse');
                $postModel          = \Pi::model('post', 'discourse');
                $topicModel         = \Pi::model('topic', 'discourse');
                $userModel          = \Pi::model('user', 'discourse');
                
                $userActionTable    = $userActionModel->getTable();
                $postTable          = $postModel->getTable();
                $topicTable         = $topicModel->getTable();
                $userTable          = $userModel->getTable();

                $select = $userActionModel->select()
                    ->columns(array(
                        'action_user_id'    => 'user_id',
                        'target_post_id'    => 'target_post_id',
                        'target_topic_id'   => 'target_topic_id',
                        'time_created'      => 'time_created',
                    ))
                    ->where(array(
                        $userActionTable . '.user_id' => $userData[id],
                        'action_type' => static::USER_ACTION_STAR,
                    ))
                    ->join(array('topicTable' => $topicTable),
                        $userActionTable . '.target_topic_id = topicTable.id',
                        array(
                            'topic_id'      => 'id',
                            'topic_title'   => 'title',
                            'topic_user_id' => 'user_id',
                        ),
                        'left'
                    )
                    ->join(array('userTable' => $userTable),
                        'topicTable.user_id = userTable.id',
                        array(
                            'topic_user_avatar' => 'avatar',
                            'topic_user_name'   => 'name'
                        ),
                        'left'
                    )
                    ->join(array('userTable2' => $userTable),
                        $userActionTable . '.user_id = userTable2.id',
                        array(
                            'action_user_avatar' => 'avatar',
                            'action_user_name'   => 'name'
                        ),
                        'left'
                    )
                    ->offset(intval(($page - 1) * static::PER_PAGE))
                    ->limit(intval(static::PER_PAGE));
                $rowset = $userActionModel->selectWith($select);
                $result = $rowset->toArray();
                
                foreach ($result as &$row) {
                    $select = $postModel ->select()
                        ->columns(array(
                            'post_user_id'      => 'user_id',
                            'post_raw'          => 'raw',
                        ))
                        ->where(array(
                            'topic_id' => $row['target_topic_id'],
                            'post_number' => 1,
                        ))
                        ->join(array('userTable' => $userTable),
                            $postTable . '.user_id = userTable.id',
                            array(
                                'post_user_avatar' => 'avatar',
                                'post_user_name'   => 'name'
                            ),
                            'left'
                        );
                    $rowset = $postModel->selectWith($select);
                    $postData = $rowset->current()->toArray();
                    $row = array_merge($row, $postData);
                    $row['action_type']         = 'star';
                    $row['time_created']        = date('Y-m-d h:i:s', $row['time_created']);
                
                }
                unset($row);

                return $result;
                break;
            case static::ACTION_FILTER_TOPIC:
                $postModel          = \Pi::model('post', 'discourse');
                $topicModel         = \Pi::model('topic', 'discourse');
                $userModel          = \Pi::model('user', 'discourse');
                
                $postTable          = $postModel->getTable();
                $topicTable         = $topicModel->getTable();
                $userTable          = $userModel->getTable();
                
                $select = $topicModel->select()
                    ->columns(array(
                        'topic_id'      => 'id',
                        'topic_title'   => 'title',
                        'time_created'  => 'time_created',
                    ))
                    ->where(array(
                        $topicTable . '.user_id' => $userData[id],
                    ))
                    ->join(array('userTable' => $userTable),
                        $topicTable . '.user_id = userTable.id',
                        array(
                            'topic_user_avatar' => 'avatar',
                            'topic_user_name'   => 'name'
                        ),
                        'left'
                    )
                    ->offset(intval(($page - 1) * static::PER_PAGE))
                    ->limit(intval(static::PER_PAGE));
                
                $rowset = $topicModel->selectWith($select);
                $result = $rowset->toArray();
                
                foreach ($result as &$row) {
                    $select = $postModel ->select()
                        ->columns(array(
                            'post_user_id'      => 'user_id',
                            'post_raw'          => 'raw',
                        ))
                        ->where(array(
                            'topic_id' => $row['topic_id'],
                            'post_number' => 1,
                        ))
                        ->join(array('userTable' => $userTable),
                            $postTable . '.user_id = userTable.id',
                            array(
                                'post_user_avatar' => 'avatar',
                                'post_user_name'   => 'name'
                            ),
                            'left'
                        );
                    $rowset = $postModel->selectWith($select);
                    $postData = $rowset->current()->toArray();
                    $row = array_merge($row, $postData);
                    $row['action_type']         = 'topic';
                    $row['time_created']        = date('Y-m-d h:i:s', $row['time_created']);
                
                }
                unset($row);
                
                d($result);
                return $result;
                break;
            case static::ACTION_FILTER_POST:
                $postModel          = \Pi::model('post', 'discourse');
                $topicModel         = \Pi::model('topic', 'discourse');
                $userModel          = \Pi::model('user', 'discourse');
                
                $postTable          = $postModel->getTable();
                $topicTable         = $topicModel->getTable();
                $userTable          = $userModel->getTable();
                
                $select = $postModel->select()
                    ->columns(array(
                        'post_raw' => 'raw',
                        'post_user_id' => 'user_id',
                        'time_created' => 'time_created',
                    ))
                    ->where(array(
                        $postTable . '.user_id = ' . $userData[id],
                        $postTable . '.post_number > 1',
                    ))
                    ->join(array('topicTable' => $topicTable),
                        $postTable . '.topic_id = topicTable.id',
                        array(
                            'topic_id'      => 'id',
                            'topic_title'   => 'title',
                            'topic_user_id' => 'user_id',
                        ),
                        'left'
                    )
                    ->join(array('userTable' => $userTable),
                        $postTable . '.user_id = userTable.id',
                        array(
                            'post_user_avatar' => 'avatar',
                            'post_user_name'   => 'name'
                        ),
                        'left'
                    )
                    ->offset(intval(($page - 1) * static::PER_PAGE))
                    ->limit(intval(static::PER_PAGE));
                
                $rowset = $postModel->selectWith($select);
                $result = $rowset->toArray();
                
                foreach ($result as &$row) {
                    $row['action_type']         = 'post';
                    $row['time_created']        = date('Y-m-d h:i:s', $row['time_created']);
                }
                unset($row);
                
                d($result);
                return $result;
                
                break;
            case static::ACTION_FILTER_RESPONSE:
                $userActionModel    = \Pi::model('user_action', 'discourse');
                $postModel          = \Pi::model('post', 'discourse');
                $topicModel         = \Pi::model('topic', 'discourse');
                $userModel          = \Pi::model('user', 'discourse');
                
                $userActionTable    = $userActionModel->getTable();
                $postTable          = $postModel->getTable();
                $topicTable         = $topicModel->getTable();
                $userTable          = $userModel->getTable();
                
                $select = $userActionModel->select()
                    ->columns(array(
                        'action_user_id'    => 'user_id',
                        'target_topic_id'   => 'target_topic_id',
                        'target_post_id'    => 'target_post_id',
                    ))
                    ->where(array(
                        $userActionTable . '.user_id' => $userData[id],
                        'action_type'   => static::USER_ACTION_RESPONSE,
                    ))
                    ->join(array('topicTable' => $topicTable),
                        $userActionTable . '.target_topic_id = topicTable.id',
                        array(
                            'topic_id'      => 'id',
                            'topic_title'   => 'title',
                            'topic_user_id' => 'user_id',
                        ),
                        'left'
                    )
                    ->join(array('postTable' => $postTable),
                        $userActionTable . '.target_post_id = postTable.id',
                        array(
                            'post_id'   => 'id',
                            'post_raw'  => 'raw',
                        ),
                        'left'
                    )
                    ->join(array('userTable' => $userTable),
                        'topicTable.user_id = userTable.id',
                        array(
                            'topic_user_avatar' => 'avatar',
                            'topic_user_name'   => 'name'
                        ),
                        'left'
                    )
                    ->join(array('userTable2' => $userTable),
                        $userActionTable . '.target_user_id = userTable2.id',
                        array(
                            'post_user_id'      => 'id',
                            'post_user_avatar'  => 'avatar',
                            'post_user_name'    => 'name'
                        ),
                        'left'
                    )
                    ->offset(intval(($page - 1) * static::PER_PAGE))
                    ->limit(intval(static::PER_PAGE));
                $rowset = $userActionModel->selectWith($select);
                $result = $rowset->toArray();
                
                foreach ($result as &$row) {
                    $row['action_type']         = 'response';
                    $row['time_created']        = date('Y-m-d h:i:s', $row['time_created']);
                }
                unset($row);
                
                d($result);
                return $result;
                break;
        }
    }
    
    function getUserActionCount($user_id)
    {
        $userData = Pi::service('api')->discourse(array('user', 'getUserInfo'), $user_id);
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
                    break;
                case static::USER_ACTION_LIKE:
                    $count['like'] = $data['count'];
                    break;
                case static::USER_ACTION_STAR:
                    $count['star'] = $data['count'];
                    break;
                case static::USER_ACTION_RESPONSE:
                    $count['response'] = $data['count'];
                    break;
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
                    'user_id = ' . $userData[id],
                    'post_number > 1',
                ));
        $postCountSet = $postModel->selectWith($select);
        $postCount = $postCountSet->current()->toArray();
        $count['post'] = $postCount['count'];
        
        return $count;
    }
    
    public function timeFromNow($time)
    {
        $seconds = (time() - $time);
        if($seconds > 31536000) {
            return intval($seconds / 31536000) . 'Y';
        } else if ($seconds > 86400) { 
            return intval($seconds / 86400) . 'd';
        } else if ($seconds > 3600){
            return intval($seconds / 3600) . 'h';
        } else if ($seconds > 60){
            return intval($seconds / 60) . 'm';
        } else {
            return $seconds . 's';
        }
    }
}