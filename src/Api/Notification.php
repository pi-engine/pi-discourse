<?php
/* << replace >>*/

namespace Module\Discourse\Api;

use Pi;
use Pi\Application\AbstractApi;
use Pi\Db\RowGateway\RowGateway;

class Notification extends AbstractApi
{
    const PER_PAGE = 20;
    
    public function createNotification($targetUserId, $topicId, $postNumber, $topicTitle, $displayUsername)
    {
        $notificationModel = \Pi::model('notification', 'discourse');
        $data = array(
            "topic_title"       => $topicTitle,
            "display_username"  => $displayUsername,
        );
        $rowData = array(
            'user_id'           => $targetUserId,
            'data'              => json_encode($data),
            'topic_id'          => $topicId,
            'post_number'       => $postNumber,
            'notification_type' => 1,
            'time_created'      => time(),
            'time_updated'      => time(),
        );
        
        $notificationRow = $notificationModel->createRow($rowData);
        $notificationRow->save();
    }
    
    public function getUnreadNotification($userId)
    {
        $notificationModel = \Pi::model('notification', 'discourse');
        $select = $notificationModel->select()->where(array(
                'user_id'   => intval($userId),
                'read'      => false,
            ))
            ->limit(static::PER_PAGE);
        $notificationRowset = $notificationModel->selectWith($select);
        $result = $notificationRowset->toArray();
        $result = array(
            'count'         => $this->getUnreadCount($userId),
            'notifications' => $result,
        );
        return $result;
    }
    
    public function getUnreadCount($userId)
    {
        $notificationModel = \Pi::model('notification', 'discourse');
        $select = $notificationModel->select()->columns(array(
                'count' => new \Zend\Db\Sql\Expression('count(*)'),
            ))
            ->where(array(
                'user_id'   => intval($userId),
                'read'      => false,
            ))
            ->limit(static::PER_PAGE);
        $notificationRowset = $notificationModel->selectWith($select);
        $result = $notificationRowset->current()->count;
        return $result;
    }
    
    public function markNotificationReadByTopic($user_id, $topic_id)
    {
        $notificationModel = \Pi::model('notification', 'discourse');
        $notificationModel->update(
            array(
                'read' => 1,
            ),
            array(
                'topic_id'  => $topic_id,
                'user_id'   => $user_id,
            )
        );
    }
}