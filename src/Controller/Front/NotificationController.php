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

class NotificationController extends DiscourseRestfulController
{
    /**
     * /notification/ GET
     * 
     */
    public function get($id)
    {
        throw new \Zend\Mvc\Exception\DomainException('Invalid HTTP method!');
    }
    
    /**
     * /notification/ POST
     * 
     */   
    public function create($data)
    {
        throw new \Zend\Mvc\Exception\DomainException('Invalid HTTP method!');
    }
    
    /**
     * /notification/{id} PUT
     * 
     */
    public function update($id, $parsedParams)
    {
        throw new \Zend\Mvc\Exception\DomainException('Invalid HTTP method!');
    }
    
    /**
     * /notification/{id} DELETE
     * 
     */    
    public function delete($id)
    {
        throw new \Zend\Mvc\Exception\DomainException('Invalid HTTP method!');
    }
    
    /**
     * /notification/{id}/{num1}/{num2} GET
     * 
     */
    public function getMulti($user_id, $limit)
    {
        return json_encode(Pi::service('api')->discourse(array('notification', 'getUnreadNotification'), $user_id));
//        throw new \Zend\Mvc\Exception\DomainException('Invalid HTTP method!');
    }
}