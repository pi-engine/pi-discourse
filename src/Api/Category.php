<?php
/* << replace >>*/

namespace Module\Discourse\Api;

use Pi;
use Pi\Application\AbstractApi;
use Pi\Db\RowGateway\RowGateway;

class Category extends AbstractApi
{
    /** 
     * Create a category, and will check up if such name is in used.
     * 
     * @param  array $data
     * @return array
     */
    public function createCategory($data)
    {
        $userData = Pi::service('api')->discourse(array('user', 'getCurrentUserInfo'));
        
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