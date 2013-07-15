<?php
/* << replace >>*/

namespace Module\Discourse\Api;

use Pi;
use Pi\Application\AbstractApi;
use Pi\Db\RowGateway\RowGateway;

class Notification extends AbstractApi
{
    const PER_PAGE = 20;
    
    public function createNotification($userId, $data)
    {
        
    }
    
    public function getUnreadNotification($userId)
    {
        
    }
}