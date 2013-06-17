<?php
/* << replace >>*/

namespace Module\Discourse\Controller\Front;

use Pi;
use Module\Discourse\Lib\DiscourseRestfulController;

/**
 * RESTful Category API for direct query
 * 
 * Direct Query:
 * url:/category                        method:POST
 * url:/category/{id}                   method:GET
 * url:/category/{id}                   method:PUT
 * url:/category/{id}                   method:DELETE
 * url:/category/{id}/{num1}/{num2}     method:GET
 * 
 * 
 * For temporary test, see scripts in:
 * Browser Console Tests: https://github.com/pi-engine/pi-discourse/wiki/Browser-Console-Tests
 * 
 */
class CategoryController extends DiscourseRestfulController
{
    /**
     * /category POST
     * 
     */
    public function create($data)
    {
//        return json_encode($this->createCategory($data));
        return json_encode(Pi::service('api')->discourse(array('category', 'createCategory'), $data));
    }
    
    /**
     * /category/{id} PUT
     * 
     */
    public function update($id, $parsedParams)
    {
//        return json_encode($this->updateCategoryInfo($id, $parsedParams));
        return json_encode(Pi::service('api')->discourse(array('category', 'updateCategoryInfo'), $id, $parsedParams));
    }
    
    /**
     * /category/{id} DELETE
     * 
     */
    public function delete($id)
    {
//        return json_encode($this->deleteCategory($id));
        return json_encode(Pi::service('api')->discourse(array('category', 'deleteCategory'), $id));
    }
    
    /**
     * /category/{id} GET
     * 
     */
    public function get($id)
    {
//        return json_encode($this->getCategory($id));
        return json_encode(Pi::service('api')->discourse(array('category', 'getCategory'), $id));
    }
    
    /**
     * /category/{id}/{num1}/{num2} GET
     * 
     */
    public function getMulti($categoryId, $offset = 0, $limit = 20)
    {
//        return json_encode($this->getTopics($categoryId, $offset, $limit));
        return json_encode(Pi::service('api')->discourse(array('category', 'getTopics'), $categoryId, $offset, $limit));
    }
}