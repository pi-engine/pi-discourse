<?php
/* << replace >>*/

namespace Module\Discourse\Api;

use Pi;
use Pi\Application\AbstractApi;
use Pi\Db\RowGateway\RowGateway;

class PostAction extends AbstractApi
{
    const POST_ACTION_BOOKMARK  = 1;
    
    const POST_ACTION_LIKE      = 2;
    
    const USER_ACTION_BOOKMARK      = 1;
    
    const USER_ACTION_LIKE          = 2;
    
    public function handle($data)
    {
        $postModel          = \Pi::model('post', 'discourse');
        $topicModel         = \Pi::model('topic', 'discourse');
        $postActionModel    = \Pi::model('post_action', 'discourse');
        $userActionModel    = \Pi::model('user_action', 'discourse');
        
        $userData = Pi::service('api')->discourse(array('user', 'getCurrentUserInfo'));
        if ($userData['isguest']) {
            return array( 'err_msg' => "You haven't logged in." );
        }
        
        if ($data['post_id']) {
            $postRow = $postModel->find(intval($data['post_id']));
            if (!$postRow->id) {
                return array( 'err_msg' => "No such post." );
            } else if (!$data['post_action_type_id']) {
                return array( 'err_msg' => "Require post action type id." );
            }
        } else {
            return array( 'err_msg' => "Require post id." );
        }
        
        $select = $postActionModel->select()
                ->where(array(
                    'post_id'               => intval($data['post_id']),
                    'user_id'               => $userData['id'],
                    'post_action_type_id'   => intval($data['post_action_type_id']),
                ));
        $postActionRowset = $postActionModel->selectWith($select);
        $postActionRow = $postActionRowset->current();

        if (intval($data['post_action_type_id']) == static::POST_ACTION_LIKE) {
            $userActionType = static::USER_ACTION_LIKE;
        } else if (intval($data['post_action_type_id']) == static::POST_ACTION_BOOKMARK) {
            $userActionType = static::USER_ACTION_BOOKMARK;
        }
            
        if (intval($data['status'])) {
            $postActionData = array(
                'post_id'               => intval($data['post_id']),
                'user_id'               => $userData['id'],
                'post_action_type_id'   => intval($data['post_action_type_id']),
                'time_updated'          => time(),
                'time_created'          => time(),
            );
            
            if ($postActionRow) {
                return array(
                    'post'          => $postModel->find(intval($data['post_id']))->toArray(),
                    'postAction'    => $postActionRow->toArray(),
                );
            } else {
                $postActionRow = $postActionModel->createRow($postActionData);
                $postActionRow->save();
                if (intval($data['post_action_type_id']) == static::POST_ACTION_LIKE) {
                    $postModel->update(array(
                        'like_count' => new \Zend\Db\Sql\Expression('`like_count` + 1')
                    ), array('id' => intval($data['post_id'])));
                    $topicModel->update(array(
                        'like_count' => new \Zend\Db\Sql\Expression('`like_count` + 1')
                    ), array('id' => intval($postRow->topic_id)));
                }
                
                if ($userActionType) {
                    $userActionData = array(
                        'action_type'       => $userActionType,
                        'user_id'           => $userData['id'],
                        'target_post_id'    => intval($data['post_id']),
                        'target_topic_id'   => $postRow->topic_id,
                        'target_user_id'    => intval($postRow->user_id),
                        'time_created'      => time(),
                        'time_updated'      => time(),
                    );
                    $userActionRow = $userActionModel->createRow($userActionData);
                    $userActionRow->save();
                }
                
                
                return array(
                    'post'          => $postModel->find(intval($data['post_id']))->toArray(),
                    'postAction'    => $postActionRow->toArray(),
                );
            }
        } else {
            if ($postActionRow) {
                $postActionRow->delete();
                if (intval($data['post_action_type_id']) == static::POST_ACTION_LIKE) {
                    $postModel->update(array(
                        'like_count' => new \Zend\Db\Sql\Expression('`like_count` - 1')
                    ), array('id' => intval($data['post_id'])));
                    $topicModel->update(array(
                        'like_count' => new \Zend\Db\Sql\Expression('`like_count` - 1')
                    ), array('id' => intval($postRow->topic_id)));
                }
                
                $select = $userActionModel->select()
                        ->where(array(
                            'action_type'       => $userActionType,
                            'user_id'           => $userData['id'],
                            'target_post_id'    => intval($data['post_id']),
                        ));
                $userActionRowset = $userActionModel->selectWith($select);
                $userActionRowset->current()->delete();
                
                return array(
                    'post'          => $postModel->find(intval($data['post_id']))->toArray(),
                    'postAction'    => array(
                        'post_id' => intval($data['post_id']),
                    )
                );
            } else {
                return array(
                    'post'          => $postModel->find(intval($data['post_id']))->toArray(),
                    'postAction'    => array(
                        'post_id' => intval($data['post_id']),
                    )
                );
            }
        }
    }
}