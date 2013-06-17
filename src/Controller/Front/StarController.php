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

class StarController extends DiscourseRestfulController
{    
    /**
     * /star/ GET
     * 
     */
    public function get($id)
    {
        throw new \Zend\Mvc\Exception\DomainException('Invalid HTTP method!');
    }
    
    /**
     * /star/ POST
     * 
     */   
    public function create($data)
    {
        throw new \Zend\Mvc\Exception\DomainException('Invalid HTTP method!');
    }
    
    /**
     * /star/{id} PUT
     * 
     */
    public function update($id, $parsedParams)
    {
        return json_encode(Pi::service('api')->discourse(array('star', 'handle'), $id, $parsedParams));
//        throw new \Zend\Mvc\Exception\DomainException('Invalid HTTP method!');
    }
    
    /**
     * /star/{id} DELETE
     * 
     */    
    public function delete($id)
    {
        throw new \Zend\Mvc\Exception\DomainException('Invalid HTTP method!');
    }
    
    /**
     * /star/{id}/{num1}/{num2} GET
     * 
     * 
     */
    public function getMulti($postId, $postActionType = 1, $limit = 20)
    {
//        return json_encode($postId);
        throw new \Zend\Mvc\Exception\DomainException('Invalid HTTP method!');
    }
}