<?php
/* << replace >>*/

namespace Module\Discourse\Controller\Front;

use Pi;
use Module\Discourse\Lib\DiscourseRestfulController;

class UserController extends DiscourseRestfulController
{
    public function initAccount($piAccountInfo)
    {
        $userData = $piAccountInfo->account;
        $data = array( 
                    'id'        => $userData->id,
                    'username'  => $userData->identity,
                    'email'     => $userData->email,
                    'name'      => $userData->name,
                    'avatar'    => md5( strtolower( trim( $userData->email ) ) ),
                );
        $row = \Pi::model('user', 'discourse')->createRow($data);
        $row->save();
        if (!$row->time_updated) {
            return false;
        } else {
            return true;
        }
    }
    
    public function isAdmin()
    {
        return false;
    }
    
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
    

    
    public function getUserInfo($id)
    {
        $model = \Pi::model('user', 'discourse');
        $rowset = $model->select(array('id = ?' => $id));
        $userInfo = $rowset->toArray();
        return $userInfo[0];
    }

    public function updateUserInfoAction($id)
    {
        return $this->params('id');
    }
   
    public function deleteUserAction($id)
    {
        return $this->params('id');
    }
 
    public function getCurrentUserInfo()
    {
        $default = array(
                    'name'      => 'guest',
                    'isguest'   => true,
                );
        if (Pi::service('authentication')->hasIdentity()) {
            $piAccountInfo = Pi::registry('user');
            $piAccountId = $piAccountInfo->account->id;
            $userData = $this->getUserInfo($piAccountId);
            if(!$userData) {
                if($this->initAccount($piAccountInfo)) {
                    $userData = $this->getUserInfo($piAccountId);
                } else {
                    return $default;
                }
            }
            $result = array(
                        'id'        => $userData['id'],
                        'username'  => $userData['username'],
                        'email'     => $userData['email'],
                        'name'      => $userData['name'],
                        'isguest'   => false,
                    );
            return $result;
        } else {
            return $default;
        }     
    }
}
