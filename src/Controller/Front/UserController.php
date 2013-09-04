<?php
/* << replace >>*/

namespace Module\Discourse\Controller\Front;

use Pi;
use Module\Discourse\Lib\DiscourseRestfulController;

class UserController extends DiscourseRestfulController
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
        throw new \Zend\Mvc\Exception\DomainException('Invalid HTTP method!');
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
//        $this->view()->setLayout('../module/demo/template/front/layout-front');
//        $this->view()->setLayout('layout-front');
//        $this->view()->setLayout('../module/demo/template/front/layout-front');
//        Pi::service('theme')->setTheme('discourse');
        
        if (Pi::service('authentication')->hasIdentity()) {
            $piAccountInfo = Pi::registry('user');
            $piAccountId = $piAccountInfo->account->id;
            $userData = $this->getUserInfo($piAccountId);
            if(!$userData) {
                if($this->initAccount($piAccountInfo)) {
                    $userData = $this->getUserInfo($piAccountId);
                } else {
                    return 'account initial failed';
                }
            }
            if ($this->isAdmin()) {
                return json_encode($this->getUserInfo($id));
            } else {
                if ($id == $piAccountId) {
                    return json_encode($userData);
                } else {
                    return 'no permission';
                }
            }
        } else {
            return 'no permission';
        }     
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
