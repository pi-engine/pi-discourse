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
//        if (Pi::service('authentication')->hasIdentity()) {
//            $piAccountInfo = Pi::registry('user');
//            $piAccountId = $piAccountInfo->account->id;
//            $userData = $this->getUserInfo($piAccountId);
//            
//            if(!$userData) {
//                if($this->initAccount($piAccountInfo)) {
//                    $userData = $this->getUserInfo($piAccountId);
//                } else {
//                    return 'account initial failed';
//                }
//            }
//            if ($this->isAdmin()) {
//                return json_encode($this->getUserInfo($id));
//            } else {
//                if ($id == $piAccountId) {
//                    return json_encode($userData);
//                } else {
//                    return 'no permission';
//                }
//            }
//        } else {
//            return 'no permission';
//        }
        $currentUserData = $this->getCurrentUserInfo();
        $userData = $this->getUserInfo($id);
        
        $userData['time_from_created']      = $this->timeFromNow($userData['time_created']);
        $userData['time_from_last_posted']  = $this->timeFromNow($userData['time_last_posted']);
        $userData['time_from_last_seen']    = $this->timeFromNow($userData['time_last_seen']);
        $userData['time_created']           = date('Y-m-d', $userData['time_created']);
        $userData['time_last_posted']       = date('Y-m-d', $userData['time_last_posted']);
        $userData['time_last_seen']         = date('Y-m-d', $userData['time_last_seen']);

//        d($userData);
        
        $result = array(
            'id'                    => $userData['id'],
            'username'              => $userData['username'],
            'email'                 => $userData['email'],
            'name'                  => $userData['name'],
            'avatar'                => $userData['avatar'],
            'time_created'          => $userData['time_created'],
            'time_last_posted'      => $userData['time_last_posted'],
            'time_last_seen'        => $userData['time_last_seen'],
            'time_from_created'     => $userData['time_from_created'],
            'time_from_last_posted' => $userData['time_from_last_posted'],
            'time_from_last_seen'   => $userData['time_from_last_seen']
        );
        
        return $result;
//            
//        if ($this->isAdmin() || $id == $currentUserData['id']) {
//            $result = array(
//                        'id'        => $userData['id'],
//                        'username'  => $userData['username'],
//                        'email'     => $userData['email'],
//                        'name'      => $userData['name'],
//                        'avatar'    => $userData['avatar'],
//                    );
//            return $result;
//        } else {
//            $result = array(
//                        'id'        => $userData['id'],
//                        'username'  => $userData['username'],
//                        'email'     => $userData['email'],
//                        'name'      => $userData['name'],
//                        'avatar'    => $userData['avatar'],
//                    );
//            return $result;
//        }
        
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
            $piAccountInfo = Pi::service('user')->getUser();
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
                        'admin'     => intval($userData['admin']),
                        'isguest'   => false,
                    );
            return $result;
        } else {
            return $default;
        }     
    }
    
    public function timeFromNow($time)
    {
        $seconds = (time() - $time);
        if($seconds > 31536000) {
            return intval($seconds / 31536000) . 'Y';
        } else if ($seconds > 86400) { 
            return intval($seconds / 86400) . 'd';
        } else if ($seconds > 3600){
            return intval($seconds / 3600) . 'h';
        } else if ($seconds > 60){
            return intval($seconds / 60) . 'm';
        } else {
            return $seconds . 's';
        }
    }
}