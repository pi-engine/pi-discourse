<?php
/* << replace >>*/

namespace Module\Discourse\Controller\Front;

use Pi;
use Module\Discourse\Lib\DiscourseRestfulController;

/**
 * RESTful Topic API for direct query 
 * 
 * Direct Query:
 * url:/post                        method:POST
 * url:/post/{id}                   method:GET
 * url:/post/{id}                   method:PUT
 * url:/post/{id}                   method:DELETE
 * 
 * For temporary test, see scripts in:
 * Browser Console Tests: https://github.com/pi-engine/pi-discourse/wiki/Browser-Console-Tests
 * 
 */

class PostController extends DiscourseRestfulController
{
    /**
     * /post/{id} GET
     * 
     * not get itself but get its replies
     * 
     */
    public function get($id)
    {
        return json_encode(Pi::service('api')->discourse(array('post', 'getPost'), $id));
    }
    
    /**
     * /post POST
     * 
     */   
    public function create($data)
    {
        return json_encode(Pi::service('api')->discourse(array('post', 'createPost'), $data));
    }
    
    /**
     * /post/{id} PUT
     * 
     */
    public function update($id, $parsedParams)
    {
        return json_encode(Pi::service('api')->discourse(array('post', 'updatePost'), $id, $parsedParams));
    }
    
    /**
     * /post/{id} DELETE
     * 
     */    
    public function delete($id)
    {
        return json_encode(Pi::service('api')->discourse(array('post', 'deletePost'), $id));
    }
    
    /**
     * /post/{id}/{num1}/{num2} GET
     * 
     */
    public function getMulti($postId, $offset = 0, $limit = 20)
    {
        return json_encode(Pi::service('api')->discourse(array('post', 'getPosts'), $postId, $offset, $limit));
    }
}