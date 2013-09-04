<?php
/* << replace >>*/

namespace Module\Discourse\Controller\Front;

use Pi;
use Module\Discourse\Lib\DiscourseRestfulController;

class UserActionController extends DiscourseRestfulController
{
    /*
     * /user GET
     * 
     */
    public function getList()
    {
        throw new \Zend\Mvc\Exception\DomainException('Invalid HTTP method!');
    }
   
    /*
     * /user/{id}/{offset}/{limit} GET
     * 
     */
    public function getMulti($filterId, $offset, $limit)
    {
        return json_encode(Pi::service('api')->discourse(array('userAction', 'getUserAction'), $filterId, $offset, $limit));
    }
    
    /*
     * /user POST
     * 
     */
    public function create($request)
    {
        throw new \Zend\Mvc\Exception\DomainException('Invalid HTTP method!');
    }
    
    /*
     * /user/{id} GET
     * 
     */
    public function get($id)
    {
        throw new \Zend\Mvc\Exception\DomainException('Invalid HTTP method!');
    }

    /*
     * /user/{id} PUT
     * 
     */
    public function update($id, $data)
    {
        throw new \Zend\Mvc\Exception\DomainException('Invalid HTTP method!');
    }
    
    /*
     * /user/{id} DELETE
     * 
     */
    public function delete($id)
    {
        throw new \Zend\Mvc\Exception\DomainException('Invalid HTTP method!');
    }
    
}
