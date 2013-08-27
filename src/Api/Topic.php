<?php
/* << replace >>*/

namespace Module\Discourse\Api;

use Pi;
use Pi\Application\AbstractApi;
use Pi\Db\RowGateway\RowGateway;

class Topic extends AbstractApi
{
    const POST_ACTION_BOOKMARK  = 1;
    
    const POST_ACTION_LIKE      = 2;
    
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
     * get topics with offset, limit and category filter
     * 
     * @param  mixed $categoryId
     * @param  mixed $offset = 0
     * @param  mixed $limit = 20
     * @return array
     */
    public function getTopics($categoryId, $offset = 0, $limit = 20)
    {
        $topicModel     = \Pi::model('topic', 'discourse');
        
        $select = $topicModel->select()
                ->where(array(
                        'category_id'   => intval($categoryId),
                        'visible'       => 1
                ))
                ->order(array(
                        'pinned DESC', 
                        'time_updated DESC'
                ))
                ->offset(intval($offset))
                ->limit(intval($limit));
        $rowset = $topicModel->selectWith($select);
        $topics = $rowset->toArray();
        
        if (empty($topics)) {    
            return array( 'err_msg' => "No more topics." );
        }
        
        // if loggen in
        $userData = Pi::service('api')->discourse(array('user', 'getCurrentUserInfo'));
        if(!$userData['isguest']) {
            $topicIds = array();
            foreach($topics as $topic) {
                 array_push($topicIds, $topic['id']);
            }
            $topicUserModel = \Pi::model('topic_user', 'discourse');
            $select = $topicUserModel->select()
                    ->where(array(
                        'user_id'   => intval($userData['id']),
                        'topic_id'  => $topicIds,
                    ));
            $rowset = $topicUserModel->selectWith($select);
            $topicUserResults = $rowset->toArray();
            foreach($topicUserResults as $topicUserResult) {
                foreach($topics as &$topic) {
                    if($topic['id'] == $topicUserResult['topic_id']) {
                        $topic['starred'] = $topicUserResult['starred'];
                        break;
                    }
                }
                unset($topic);
            }
        }
        
        foreach($topics as &$topic) {
            if (!isset($topic['starred'])) {
                $topic['starred'] = 0;
            }
            $topic['time_from_created']     = $this->timeFromNow($topic['time_created']);
            $topic['time_from_last_posted'] = $this->timeFromNow($topic['time_last_posted']);
            $topic['time_created']          = date('Y-m-d h:i:s', $topic['time_created']);
            $topic['time_updated']          = date('Y-m-d h:i:s', $topic['time_updated']);
            $topic['time_last_posted']      = date('Y-m-d h:i:s', $topic['time_last_posted']);
            $topic['starred']               = intval($topic['starred']);
            $topic['pinned']                = intval($topic['pinned']);
            $topic['closed']                = intval($topic['closed']);
            $topic['visible']               = intval($topic['visible']);
        }
        unset($topic);
        
//        d($topics);
        return $topics;
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
        $userData = Pi::service('api')->discourse(array('user', 'getCurrentUserInfo'));
        if ($userData['isguest']) {
            return array( 'err_msg' => "You haven't logged in." );
        }
        
        if (isset($data['category_id'])) {
            $categoryModel = \Pi::model('category', 'discourse');
            $categoryRow = $categoryModel->find(intval($data['category_id']));
            if(!$categoryRow->id) {
                return array( 'err_msg' => "No such category." );
            }
        } else {
            return array( 'err_msg' => "You must set a category." );
        }
        
        // here should add content checking 
        if ('' === trim($data['content_raw'])) {
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

            //return true;

            $newData = $topicModel->find($id)->toArray();
            return $newData;
        }
    }
    
    /**
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
    
    protected function timeFromNow($time)
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