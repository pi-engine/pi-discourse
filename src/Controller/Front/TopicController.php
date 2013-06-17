<?php
/* << replace >>*/

namespace Module\Discourse\Controller\Front;

use Pi;
use Module\Discourse\Lib\DiscourseRestfulController;

/**
 * RESTful Topic API for direct query
 * 
 * Direct Query:
 * url:/topic                       method:POST
 * url:/topic/{id}                  method:GET
 * url:/topic/{id}                  method:PUT
 * url:/topic/{id}                  method:DELETE
 * url:/topic/{id}/{num1}/{num2}    method:GET
 * 
 * 
 * For temporary test, see scripts in:
 * Browser Console Tests: https://github.com/pi-engine/pi-discourse/wiki/Browser-Console-Tests
 * 
 */

class TopicController extends DiscourseRestfulController
{
    /**
     * /topic/{id} GET
     * 
     */
    public function get($id)
    {
//        return json_encode($this->getTopic($id));
        return json_encode(Pi::service('api')->discourse(array('topic', 'getTopic'), $id));
    }
    
    /**
     * /topic POST
     * 
     */   
    public function create($data)
    {
//        return json_encode($this->createTopic($data));
        return json_encode(Pi::service('api')->discourse(array('topic', 'createTopic'), $data));
    }
    
    /**
     * /topic/{id} PUT
     * 
     */
    public function update($id, $parsedParams)
    {
//        return json_encode($this->updateTopic($id, $parsedParams));
        return json_encode(Pi::service('api')->discourse(array('topic', 'updateTopic'), $id, $parsedParams));
    }
    
    /*
     * /topic/{id} DELETE
     * 
     */    
    public function delete($id)
    {
//        return json_encode($this->deleteTopic($id));
        return json_encode(Pi::service('api')->discourse(array('topic', 'deleteTopic'), $id));
    }
}
