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
        return json_encode(Pi::service('api')->discourse(array('postAction', 'handleAction'), $data));
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
//        return json_encode($postId);
//        throw new \Zend\Mvc\Exception\DomainException('Invalid HTTP method!');
    }
}