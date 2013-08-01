<?php
/* << replace >>*/

namespace Module\Discourse\Api;

use Pi;
use Pi\Application\AbstractApi;
use Pi\Db\RowGateway\RowGateway;

class Post extends AbstractApi
{
    const POST_ACTION_BOOKMARK  = 1;
    
    const POST_ACTION_LIKE      = 2;
    
    const USER_ACTION_RESPONSE  = 4;
    
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
        
        $postModel          = \Pi::model('post', 'discourse');
        $topicModel         = \Pi::model('topic', 'discourse');
        $userActionModel    = \Pi::model('user_action', 'discourse');
        
        $userData = Pi::service('api')->discourse(array('user', 'getCurrentUserInfo'));
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
        
        $userActionData = array(
            'action_type'       => static::USER_ACTION_RESPONSE,
            'target_topic_id'   => intval($data['topic_id']),
            'target_user_id'    => $userData['id'],
            'time_created'      => time(),
            'time_updated'      => time(),
        );
        $notificationData = array(
            'replied_topic_user_id' => $topicRow->user_id
        );

        if (isset($data['reply_to_post_id'])) {
            $repliedPostRow = $postModel->find(intval($data['reply_to_post_id']));
            if ($repliedPostRow->user_id != $notificationData['replied_topic_user_id']) {
                $notificationData['replied_post_user_id'] = $repliedPostRow->user_id;
            }
            if (!$repliedPostRow->id) {
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
                $userActionData['user_id'] = $repliedPostRow->user_id;
            }
        } else {
            $select = $postModel->select()->where(array(
                'topic_id'      => intval($data['topic_id']), 
                'post_number'   => 1,
            ));
            $postRowSet = $postModel->selectWith($select);
            $postRow = $postRowSet->current();
            $userActionData['user_id'] = $postRow->user_id;
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
            $userActionData['target_post_id'] = $postRow->id;
            $userActionRow = $userActionModel->createRow($userActionData);
            $userActionRow->save();
            
            Pi::service('api')->discourse(
                array('notification', 'createNotification'),
                $notificationData['replied_topic_user_id'],
                $data['topic_id'], 
                $postRow->post_number, 
                $topicRow->title,
                $userData['name']
            );

            
            if($replyToPostId) {
                if ($notificationData['replied_post_user_id']) {
                    Pi::service('api')->discourse(
                        array('notification', 'createNotification'),
                        $notificationData['replied_post_user_id'],
                        $data['topic_id'], 
                        $postRow->post_number, 
                        $topicRow->title,
                        $userData['name']
                    );
                }
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

    /**
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
        
        if (empty($posts)) {
            return array( 'err_msg' => "No more posts." );
        }
        
        foreach($posts as &$post) {
            $post['time_from_created']      = $this->timeFromNow($post['time_created']);
            $post['time_created']           = date('Y-m-d h:i:s', $post['time_created']);
            $post['time_updated']           = date('Y-m-d h:i:s', $post['time_updated']);
            $post['post_number']            = intval($post['post_number']);
        }
        unset($post);
        
        
        /**
         * if logged in, add some info.
         */
        $userData = Pi::service('api')->discourse(array('user', 'getCurrentUserInfo'));
        if(!$userData['isguest']) {
            /**
             * add post action info(like, bookmark, etc.)
             */
            $postIds = array();
            foreach($posts as $post) {
                 array_push($postIds, $post['id']);
            }
            $postActionModel = \Pi::model('post_action', 'discourse');
            $select = $postActionModel->select()
                    ->where(array(
                        'user_id'               => intval($userData['id']),
                        'post_id'               => $postIds,
                    ));
            $rowset = $postActionModel->selectWith($select);
            $postActionResults = $rowset->toArray();
            foreach ($postActionResults as $postAction) {
                switch ($postAction['post_action_type_id']) {
                    case static::POST_ACTION_BOOKMARK:
                        foreach ($posts as &$post) {
                            if ($post['id'] == $postAction['post_id']) {
                                $post['isBookmarked'] = 1;
                            }
                        }
                        unset($post);
                    case static::POST_ACTION_LIKE:
                        foreach ($posts as &$post) {
                            if ($post['id'] == $postAction['post_id']) {
                                $post['isLiked'] = 1;
                            }
                        }
                        unset($post);
                }
            }
            
            /**
             * update topic user info(last read post, last view time, etc.)
             */
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
        
        /**
         * add user info
         */
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