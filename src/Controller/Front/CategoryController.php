<?php
/* << replace >>*/

namespace Module\Discourse\Controller\Front;

use Pi;
use Module\Discourse\Lib\DiscourseRestfulController;
use Module\Discourse\Controller\Front\UserController as UC;

/**
 * RESTful Category API for direct query or functions used by other controllers
 * 
 * Direct Query:
 * url:/category                        method:POST
 * url:/category/{id}                   method:GET
 * url:/category/{id}                   method:PUT
 * url:/category/{id}                   method:DELETE
 * url:/category/{id}/{num1}/{num2}     method:GET
 * 
 * Functions used by other controllers:
 * public function allCategories()
 * public function allTopTopics($categories)
 * public function getTopics($categoryId, $offset = 0, $limit = 20)
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
        return json_encode($this->createCategory($data));
    }
    
    /**
     * /category/{id} PUT
     * 
     */
    public function update($id, $parsedParams)
    {
        return json_encode($this->updateCategoryInfo($id, $parsedParams));
    }
    
    /**
     * /category/{id} DELETE
     * 
     */
    public function delete($id)
    {
        return json_encode($this->deleteCategory($id));
    }
    
    /**
     * /category/{id} GET
     * 
     */
    public function get($id)
    {
        return json_encode($this->getCategory($id));
    }
    
    /**
     * /category/{id}/{num1}/{num2} GET
     * 
     */
    public function getMulti($categoryId, $offset = 0, $limit = 20)
    {
        return json_encode($this->getTopics($categoryId, $offset, $limit));
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
        
        // if loggen in
        $uc = new UC();
        $userData = $uc->getCurrentUserInfo();
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
            if(!isset($topic['starred'])) {
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
     * Create a category, and will check up if such name is in used.
     * 
     * @param  array $data
     * @return array
     */
    public function createCategory($data)
    {
        $uc = new UC();
        $userData = $uc->getCurrentUserInfo();
        
        if($userData['isguest']) {
            return array( 'err_msg' => "You haven't logged in." );
        }
        
        $categoryModel = \Pi::model('category', 'discourse');
 
        $existRow = $categoryModel->find($data['name'], 'name');
        if($existRow->id) {
            return array( 'err_msg' => "Duplicated category name." );
        }
        
        $addiData = array(
                       'topic_count'  => 1,
                       'time_created' => time(),
                       'time_updated' => time(),
                       'user_id'      => $userData['id'],
                     );
        
        $categoryData = array_merge($data, $addiData);
        
        $categoryRow = $categoryModel->createRow($categoryData);
        $categoryRow->save();
        
        if (!$categoryRow->id) {
            return false;
        } else {
            $topicModel = \Pi::model('topic', 'discourse');
            $initTopicData = array(
                                'category_id'       => intval($categoryRow->id),
                                'title'             => 'Definition for ' . $categoryData['name'],
                                'time_created'      => time(),
                                'time_updated'      => time(),
                                'time_last_posted'  => time(),
                                'posts_count'       => 1,
                                'user_id'           => $userData['id'],
                                'last_post_user_id' => $userData['id'],
                                'pinned'            => 1,
                            );
            $topicRow = $topicModel->createRow($initTopicData);
            $topicRow->save();
            if(!$topicRow->id) {
                $categoryModel->find($categoryRow->id)->delete();
                return false;
            } else {
                $postModel = \Pi::model('post', 'discourse');
                $initPostData = array(
                        'user_id'       => $userData['id'],
                        'topic_id'      => intval($topicRow->id),
                        'post_number'   => 1,
                        'raw'           => '<p>[Replace this first paragraph with a short description of your new category. Try to keep it below 200 characters.]</p><p>Use this space below for a longer description, as well as to establish any rules or discussion!</p>',
                        'cooked'        => '',
                        'time_created'  => time(),
                        'time_updated'  => time(),
                    );
                $postRow = $postModel->createRow($initPostData);
                $postRow->save();
                if(!$postRow->id) {
                    $topicModel->find($topicRow->id)->delete();
                    $categoryModel->find($categoryRow->id)->delete();
                    return false;
                } else {
                    return true;
                }
            }
        }
    }
    
    /** 
     * Update a category, only allow to change these colums:
     * `name`, `color`, `slug`
     * 
     * @param  mixed $id
     * @param  array $parsedParams
     * @return array
     */
    public function updateCategoryInfo($id, $parsedParams)
    {
        $data = $parsedParams;
        if( isset($data['id']) 
            || isset($data['last_topic_id'])
            || isset($data['top1_topic_id'])
            || isset($data['top2_topic_id'])
            || isset($data['topic_count'])
            || isset($data['time_created'])
            || isset($data['time_updated'])
            || isset($data['user_id'])
            || isset($data['topics_year'])
            || isset($data['topics_month'])
            || isset($data['topics_week'])
        ) {
            return array( 'err_msg' => "You are forbidden to change some columns." );
        }
            
        $model = \Pi::model('category', 'discourse');
 
        $existRow = $model->find($data['name'], 'name');
        if(!empty($existRow->id) && $existRow->id != $id) {
            return array( 'err_msg' => "Duplicated category name." );
        }
        
        $addiData = array(
                       'time_updated' => time(),
                     );
        
        $data = array_merge($data, $addiData);
        
        $model->update($data, array('id' => $id));
        
        return true;
    }

    /** 
     * Delete a category
     * 
     * THIS FUNCTION IS NOT FINISHED YET!
     * should delete all topics and posts
     * 
     * @param  mixed $id
     * @return array
     */
    public function deleteCategory($id)
    {
        $model = \Pi::model('category', 'discourse');
        $row = $model->find($id);
        if($row->id) {
            $row->delete();
            \Pi::model('topic', 'discourse')->delete(array('category_id' => $id));
            
            /**
             * select topic ids and delete with multi filter
             * 
             * 
             */
            
//            \Pi::model('post', 'discourse')->delete(array('category_id' => $id));
            
        } else {
            return array( 'err_msg' => "No such category." );
        }
        
        $row = $model->find($id);
        if($row->id) {
            return array( 'err_msg' => "Delete failed." );
        } else {
            return true;
        }
    }
    
    /**
     * Get a category's info by id
     * 
     * @param  mixed $id
     * @return array
     */
    public function getCategory($id)
    {
        $model = \Pi::model('category', 'discourse');
        $row = $model->find($id);
        return $row->toArray();
    }
    
    /**
     * Get all categories
     * Not ordered yet, perhaps will add some rules in the future
     * 
     * @return array
     */
    public function allCategories()
    {
        $model = \Pi::model('category', 'discourse');
        
        $rowset = $model->select(array('1 = ?' => 1));
        $categories = $rowset->toArray();
        
        return $categories;
    }
    
    /**
     * Get latest updated 6 topics in each categories
     * '6' is hard coding now, it should use config data.
     * 
     * @param  array $categories
     * @return array
     */
    public function allTopTopics($categories)
    {
        $model = \Pi::model('topic', 'discourse');
        
        $topics = array();
        
        foreach($categories as $category) {
            $select = $model->select()
                    ->where(array('category_id' => $category['id'] ))
                    ->order(array('time_updated DESC'))
                    ->limit(6);
            $rowset = $model->selectWith($select);
            $data = array(  'category_id' => $category['id'],
                            'topics'=> $rowset->toArray() );
            array_push($topics, $data);
        }
        
        return $topics;
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