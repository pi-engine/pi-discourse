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
        return json_encode($this->getTopic($id));
    }
    
    /**
     * /topic POST
     * 
     */   
    public function create($data)
    {
        return json_encode($this->createTopic($data));
    }
    
    /**
     * /topic/{id} PUT
     * 
     */
    public function update($id, $parsedParams)
    {
        return json_encode($this->updateTopic($id, $parsedParams));;
    }
    
    /*
     * /topic/{id} DELETE
     * 
     */    
    public function delete($id)
    {
        return json_encode($this->deleteTopic($id));
    }
    
    /**
     * /topic/{id}/{num1}/{num2} GET
     * 
     */
    public function getMulti($categoryId, $offset = 0, $limit = 20)
    {
        return json_encode($this->getPosts($categoryId, $offset, $limit));
    }
    
    
    /**
     * get posts with offset, limit and topic filter
     * if logged in, update `time_last_visited` in table `topic_user`
     * 
     * @param  mixed $topicId
     * @param  mixed $offset = 0
     * @param  mixed $limit = 20
     * @return array
     */
    public function getPosts($topicId, $offset = 0, $limit = 20)
    {
        $postModel          = \Pi::model('post', 'discourse');
        $topicUserModel     = \Pi::model('topic_user', 'discourse');
        $userModel          = \Pi::model('user', 'discourse');
        $postReplyModel     = \Pi::model('post_reply', 'discourse');
        
        $postTable          = $postModel->getTable();
        $postReplyTable     = $postReplyModel->getTable();
        
        $select = $postModel->select()
                ->where(array('topic_id' => intval($topicId)))
                ->order(array('post_number'))
                ->offset(intval($offset))
                ->limit(intval($limit))
                ->join(array('postReplyTable' => $postReplyTable),
                       'postReplyTable.post_id = ' . $postTable . '.id', 
                       array('reply_to_post_id'), 
                       'left'
                );
        
        $rowset = $postModel->selectWith($select);
        $posts = $rowset->toArray();
        foreach($posts as &$post) {
            $post['time_from_created']      = $this->timeFromNow($post['time_created']);
            $post['time_created']           = date('Y-m-d h:i:s', $post['time_created']);
            $post['time_updated']           = date('Y-m-d h:i:s', $post['time_updated']);
        }
        unset($post);
        
        $uc = new UC();
        $userData = $uc->getCurrentUserInfo();
        if(!$userData['isguest']) {
            $topicUserModel = \Pi::model('topic_user', 'discourse');

            $lastPost = end($posts);
            
            $select = $topicUserModel->select()
                    ->where(array('topic_id' => $topicId, 'user_id' => $userData['id'] ));
            $topicUserRowset = $topicUserModel->selectWith($select);
            $topicUserRow = $topicUserRowset->toArray();
            
            if(!$topicUserRow[0]) {
                $topicUserData = array(
                                    'topic_id'              => $topicId,
                                    'user_id'               => $userData['id'],
                                    'time_last_visited'     => time(),
                                );
                $topicUserRow = $topicUserModel->createRow($topicUserData);
                $topicUserRow->save();
            } else {
                $topicUserModel->update(
                                        array(
                                            'time_last_visited' => time(),
                                        ),
                                        array(
                                            'topic_id'  => $topicId,
                                            'user_id'   => $userData['id'],
                                        )
                                    );
            }
        }
        
        
        $users = array();
        
        foreach($posts as $post) {
            if(!in_array($post['user_id'], $users)) {
                array_push($users, $post['user_id']);
            }
        }
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
        
        return array( 'posts' => $posts, 'users' => $users );
    }
    
    /**
     * Get a topic's info by id
     * 
     * @param  mixed $id
     * @return array
     */
    public function getTopic($id)
    {
        $topicModel = \Pi::model('topic', 'discourse');
        
        $select = $topicModel->select()
                ->where(array('id' => intval($id)));
        $rowset = $topicModel->selectWith($select);
        $topics = $rowset->toArray();
        
        return $topics[0];
    }
    
    /**
     * Create a topic, if a category_id is defind, check if that category
     * exists, if a topic row create successfully, try to create the first
     * post, if the first post create unsuccessfully, delete that topic
     * and return false.
     * 
     * THIS FUNCTION IS NOT FINISHED YET!
     * should add post content checking
     * 
     * @param  array $data
     * @return array
     */
    public function createTopic($data)
    {
        $uc = new UC();
        $userData = $uc->getCurrentUserInfo();
        
        if($userData['isguest']) {
            return array( 'err_msg' => "You haven't logged in." );
        }
        
        if(isset($data['category_id'])){
            $categoryModel = \Pi::model('category', 'discourse');
            $categoryRow = $categoryModel->find(intval($data['category_id']));
            if(!$categoryRow->id) {
                return array( 'err_msg' => "No such category." );
            }
        } else {
            return array( 'err_msg' => "You must set a category." );
        }
        
        // here should add content checking 
        if('' === trim($data['content_raw'])) {
            return array( 'err_msg' => "Content is not valid." );
        }
        $contentRaw = $data['content_raw'];
        unset($data['content_raw']);
        
        $topicModel = \Pi::model('topic', 'discourse');
        
        $addiData = array(
                        'time_created'      => time(),
                        'time_updated'      => time(),
                        'time_last_posted'  => time(),
                        'posts_count'       => 1,
                        'user_id'           => $userData['id'],
                        'last_post_user_id' => $userData['id'],
                    );
        
        $data = array_merge($data, $addiData);
        $topicRow = $topicModel->createRow($data);
        $topicRow->save();
        
        if (!$topicRow->id) {
            return array( 'err_msg' => "Creation failed." );
        } else {
            $postModel = \Pi::model('post', 'discourse');
            $post = array(
                        'user_id'       => $userData['id'],
                        'topic_id'      => intval($topicRow->id),
                        'post_number'   => 1,
                        'raw'           => $contentRaw,
                        'cooked'        => '',
                        'time_created'  => time(),
                        'time_updated'  => time(),
                    );
            $postRow = $postModel->createRow($post);
            $postRow->save();
            if (!$postRow->id) {
                $topicModel->find($topicRow->id)->delete();
                return array( 'err_msg' => "Creation failed." );
            } else {
                $categoryData = array('topic_count' => $categoryRow->topic_count + 1);
                $categoryModel->update($categoryData, array('id' => $data['category_id']));
//                return true;
                $newTopic = $topicModel->find($topicRow->id)->toArray();
                $newTopic['time_from_created']     = $this->timeFromNow($newTopic['time_created']);
                $newTopic['time_from_last_posted'] = $this->timeFromNow($newTopic['time_last_posted']);
                $newTopic['time_created']          = date('Y-m-d h:i:s', $newTopic['time_created']);
                $newTopic['time_updated']          = date('Y-m-d h:i:s', $newTopic['time_updated']);
                $newTopic['time_last_posted']      = date('Y-m-d h:i:s', $newTopic['time_last_posted']);
                if(!isset($topic['starred'])) {
                    $newTopic['starred'] = 0;
                }
                return $newTopic;
            }
        }
    }

    /**
     * Update a topic, only allow to change `title`.
     * 
     * THIS FUNCTION IS NOT FINISHED YET!
     * should add topic title checking
     * 
     * @param  mixed $id
     * @param  array $parsedParams
     * @return array
     */
    public function updateTopic($id, $parsedParams)
    {
        $title = $parsedParams['title'];
        unset($parsedParams['title']);
        if(0 !== count($parsedParams)) {
            return array( 'err_msg' => "You are forbidden to change some columns." );
        }
        //should check if title is valid
        if(empty($title)) {
            return array( 'err_msg' => "Title is not valid" );
        } else {
            $topicModel = \Pi::model('topic', 'discourse');
            $existRow = $topicModel->find($id);
            if(!$existRow->id) {
                return array( 'err_msg' => "No such topic." );
            }
            
            $data = array(
                        'title'         => $title,
                        'time_updated'  => time(),
                    );
        
            $topicModel->update($data, array('id' => $id));

            return true;
        }
    }
    
    /*
     * Delete a topic and it's posts
     * 
     * @param  mixed $id
     * @return array
     */    
    public function deleteTopic($id)
    {
        $topicModel = \Pi::model('topic', 'discourse');
        $topicRow = $topicModel->find($id);
        if($topicRow->id) {
            $topicRow->delete();
            \Pi::model('post', 'discourse')->delete(array('topic_id' => $id));
        } else {
            return array( 'err_msg' => "No such topic." );
        }
        
        $topicRow = $topicModel->find($id);
        if($topicRow->id) {
            return array( 'err_msg' => "Delete failed." );
        } else {
            return true;
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
