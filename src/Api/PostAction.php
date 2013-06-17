<?php
/* << replace >>*/

namespace Module\Discourse\Api;

use Pi;
use Pi\Application\AbstractApi;
use Pi\Db\RowGateway\RowGateway;

class PostAction extends AbstractApi
{
    public function handleAction($data)
    {
        $postModel          = \Pi::model('post', 'discourse');
        $postActionModel    = \Pi::model('post_action', 'discourse');
        
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

        
        if (intval($data['status'])) {
            $postActionData = array(
                'post_id'               => intval($data['post_id']),
                'user_id'               => $userData['id'],
                'post_action_type_id'   => intval($data['post_action_type_id']),
                'time_updated'          => time(),
                'time_created'          => time(),
            );
            
            if ($postActionRow) {
                return 'already true';
            } else {
                $postActionRow = $postActionModel->createRow($postActionData);
                $postActionRow->save();
                return 'success';
            }
        } else {
            if ($postActionRow) {
                $postActionRow->delete();
                return 'deleted';
            } else {
                return 'success';
            }
        }
    }
}