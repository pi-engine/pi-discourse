<?php
/* << replace >>*/

namespace Module\Discourse\Controller\Front;

use Pi;
use Module\Discourse\Lib\DiscourseRestfulController;
use Module\Discourse\Controller\Front\UserController as UC;

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
        return json_encode($this->getPost($id));
    }
    
    /**
     * /post POST
     * 
     */   
    public function create($data)
    {
        return json_encode($this->createPost($data));
    }
    
    /**
     * /post/{id} PUT
     * 
     */
    public function update($id, $parsedParams)
    {
        return json_encode($this->updatePost($id, $parsedParams));
    }
    
    /**
     * /post/{id} DELETE
     * 
     */    
    public function delete($id)
    {
        return json_encode($this->deletePost($id));
    }
    
    /**
     * /post/{id}/{num1}/{num2} GET
     * 
     */
    public function getMulti($postId, $offset = 0, $limit = 20)
    {
//        throw new \Zend\Mvc\Exception\DomainException('Invalid HTTP method!');
        return json_encode($this->getPostReply($postId, $offset, $limit));
    }
    
    /**
     * Create a post, check if topic_id is defined, and if is is a reply to
     * another post.
     * 
     * THIS FUNCTION IS NOT FINISHED YET!
     * should add raw content checking
     * 
     * @param  array $data
     * @return array
     */  
    public function createPost($data)
    {
        $replyToPostId = null;
        
        $postModel = \Pi::model('post', 'discourse');
        $topicModel = \Pi::model('topic', 'discourse');
        
        $uc = new UC();
        $userData = $uc->getCurrentUserInfo();
        if($userData['isguest']) {
            return array( 'err_msg' => "You haven't logged in." );
        }
        
        if(isset($data['topic_id'])){
            $topicRow = $topicModel->find(intval($data['topic_id']));
            if(!$topicRow->id) {
                return array( 'err_msg' => "No such topic." );
            }
        } else {
            return array( 'err_msg' => "Require topic id." );
        }
        
        if(isset($data['reply_to_post_id'])){
            $repliedPostRow = $postModel->find(intval($data['reply_to_post_id']));
            if(!$repliedPostRow->id) {
                return array( 'err_msg' => "Replied post not exists." );
            } else {
                if( $data['topic_id'] == $repliedPostRow->topic_id) {
                    $replyToPostId = $data['reply_to_post_id'];
                    unset($data['reply_to_post_id']);
                    
                    $postData = array('reply_count' => $repliedPostRow->reply_count + 1);
                    $postModel->update($postData, array('id' => $repliedPostRow->id));
                } else {
                    return array( 'err_msg' => "Replied post doesn't exist in that topic." );
                }
            }
        }
        
        $select = $postModel->select()
                ->where(array('topic_id' => intval($data['topic_id'])));
        $postRowSet = $postModel->selectWith($select);
        $postNumber = count($postRowSet->toArray()) + 1;
        
        // should add raw content checking
        
        $addiData = array(
                    'user_id'       => $userData['id'],
                    'topic_id'      => intval($data['topic_id']),
                    'post_number'   => $postNumber,
                    'raw'           => $data['raw'],
                    'cooked'        => '',
                    'time_created'  => time(),
                    'time_updated'  => time(),
                );
        $data = array_merge($data, $addiData);
        
        $postRow = $postModel->createRow($data);
        $postRow->save();
        if (!$postRow->id) {
            return false;
        } else {
            if($replyToPostId) {
                $postReplyModel = \Pi::model('post_reply', 'discourse');
                $relationData = array(
                                    'post_id'           => $postRow->id,
                                    'reply_to_post_id'  => $replyToPostId,
                                    'time_created'      => time(),
                                );
                $postReplyRow = $postReplyModel->createRow($relationData);
                $postReplyRow->save();
                if (!$postReplyRow->id) {
                    $postModel->find($postRow->id)->delete();
                    return false;
                } else {
                    return true;
                }
            } else {
                return true;
            }
        }
    }
    
    /**
     * Update a post, only allow to change `raw`.
     * 
     * THIS FUNCTION IS NOT FINISHED YET!
     * should check if the content is valid
     * 
     * @param  mixed $id
     * @param  array $parsedParams
     * @return array
     */
    public function updatePost($id, $parsedParams)
    {
        $raw = $parsedParams['raw'];
        unset($parsedParams['raw']);
        if(0 !== count($parsedParams)) {
            return array( 'err_msg' => "You are forbidden to change some columns." );
        }
        // should check if the content is valid
        if(empty($raw)) {
            return array( 'err_msg' => "content is not valid" );
        } else {
            $postModel = \Pi::model('post', 'discourse');
            $existRow = $postModel->find($id);
            if(!$existRow->id) {
                return array( 'err_msg' => "No such post." );
            }
            
            $data = array(
                        'raw'           => $raw,
                        'cooked'        => '',
                        'time_updated'  => time(),
                    );
        
            $postModel->update($data, array('id' => $id));

            return true;
        }
    }

    /*
     * Delete a post
     * 
     * maybe should make a post invisable instead of delete it.
     * 
     * @param  mixed $id
     * @return array
     */  
    public function deletePost($id)
    {
        $postModel = \Pi::model('post', 'discourse');
        $postRow = $postModel->find($id);
        if($postRow->id) {
            $postRow->delete();
        } else {
            return array( 'err_msg' => "No such topic." );
        }
        
        $postRow = $postModel->find($id);
        if($postRow->id) {
            return array( 'err_msg' => "Delete failed." );
        } else {
            return true;
        }
        
        /*
         * maybe should delete replied posts
         * 
         */
    }
    
    /**
     * Get reply posts by id.
     * not in use
     * 
     * @param  mixed $id
     * @return array
     */
    public function getPostReply($id, $offset, $limit)
    {
        $postModel      = \Pi::model('post', 'discourse');
        $postReplyModel = \Pi::model('post_reply', 'discourse');
        $userModel      = \Pi::model('user', 'discourse');
        
        $postTable      = $postModel->getTable();
        $postReplyTable = $postReplyModel->getTable();
        
        $select = $postReplyModel->select()
                ->columns(array('post_id', 'reply_to_post_id'))
                ->where(array('reply_to_post_id' => intval($id)))
                ->join(array('postTable' => $postTable),
                       'postTable.id = ' . $postReplyTable . '.post_id', 
                       array('user_id',
                            'topic_id',
                            'post_number',
                            'raw',
                            'time_created'))
                ->offset(intval($offset))
                ->limit(intval($limit));
                
        $rowset = $postReplyModel->selectWith($select);
        $posts = $rowset->toArray();
        
        $users = array();
        
        foreach($posts as &$post) {
            $post['time_from_created'] = $this->timeFromNow($post['time_created']);
            $post['time_created'] = date('Y-m-d h:i:s', $post['time_created']);
            if(!in_array($post['user_id'], $users)) {
                array_push($users, $post['user_id']);
            }
        }
        unset($post);
        
        $select = $userModel->select()
                ->where(array( 'id' => $users ))
                ->columns(array('id', 'username', 'name', 'avatar'));

        $rowset = $userModel->selectWith($select);
        $users = $rowset->toArray();
        
        foreach($users as &$user) {
            if(empty($user['name'])) {
                $user['name'] = $user['username'];
            }
            unset($user['username']);
        }
        unset($user);
        
        return array( 'posts' => $posts, 'users' => $users);
    }
    
    public function getPost($id)
    {
        $postModel = \Pi::model('post', 'discourse');
        
        $postRow = $postModel->find($id);
        if($postRow->id){
            $post = $postRow->toArray();
            $userModel = \Pi::model('user', 'discourse');
            $userRow = $userModel->find($postRow->user_id);
            $user = $userRow->toArray();
            
            $post['time_from_created'] = $this->timeFromNow($post['time_created']);
            $post['time_created'] = date('Y-m-d h:i:s', $post['time_created']);
            
            return array( 'post' => $post, 'user' => $user);
        } else {
            return array();
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