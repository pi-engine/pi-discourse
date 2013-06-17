<?php
/* << replace >>*/

namespace Module\Discourse\Api;

use Pi;
use Pi\Application\AbstractApi;
use Pi\Db\RowGateway\RowGateway;

class User extends AbstractApi
{
    public function initAccount($piAccountInfo)
    {
        $userData = $piAccountInfo->account;
        $data = array( 
                    'id'            => $userData->id,
                    'username'      => $userData->identity,
                    'email'         => $userData->email,
                    'name'          => $userData->name,
                    'avatar'        => md5( strtolower( trim( $userData->email ) ) ),
                    'time_created'  => time(),
                    'time_updated'  => time(),
                );
        $userModel = \Pi::model('user', 'discourse');
        $row = $userModel->createRow($data)->save();
        $userRow = $userModel->find(intval($userData->id));
        if(!$userRow->id) {
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

    public function getUserInfo($id)
    {
        $model = \Pi::model('user', 'discourse');
        $rowset = $model->select(array('id = ?' => $id));
        $userInfo = $rowset->toArray();
        return $userInfo[0];
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
                        'avatar'    => $userData['avatar'],
                        'isguest'   => false,
                    );
            return $result;
        } else {
            return $default;
        }     
    }
}