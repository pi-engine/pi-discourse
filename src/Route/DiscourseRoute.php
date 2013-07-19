<?php
/* << replace >>*/

namespace Module\Discourse\Route;

use Pi\Mvc\Router\Http\Standard;
use Zend\Mvc\Router\Http\RouteMatch;
use Zend\Stdlib\RequestInterface as Request;
use Pi;

class DiscourseRoute extends Standard
{
    protected $prefix = '/discourse';

    /**
     * Default values.
     *
     * @var array
     */
    protected $defaults = array(
        'module'        => 'discourse',
        'controller'    => 'index',
        'action'        => 'index'
    );

    /**
     * match(): defined by Route interface.
     *
     * @see    Route::match()
     * @param  Request $request
     * @return RouteMatch
     */
    public function match(Request $request, $pathOffset = null)
    {
        //$method = $request->getServer('REQUEST_METHOD');
        //use $request->isGet()...
        
        $result = $this->canonizePath($request, $pathOffset);
        if (null === $result) {
            return null;
        }
        list($path, $pathLength) = $result;
        $path    = trim($path, $this->paramDelimiter);
        $matches = array();
        
        if (empty($path)) {
            $matches['controller']  = 'clientCategory';
            $matches['action']      = 'categoryList';
        } else {;
            $params = explode('/', $path);
            
            $first  = isset($params[0]) ? $params[0] : null;
            $second = isset($params[1]) ? $params[1] : null;
            $third  = isset($params[2]) ? $params[2] : null;
            $fourth = isset($params[3]) ? $params[3] : null;
//            $fifth  = isset($params[4]) ? $params[4] : null;
            
            if ('user' === $first 
                || 'userAction' === $first 
                || 'category' === $first 
                || 'topic' === $first 
                || 'post' === $first 
                || 'postAction' === $first 
                || 'star' === $first 
                || 'postReply' === $first 
                || 'notification' === $first 
                || 0
            ) {
                $matches['controller']  = $first;
                $matches['action']      = null;
                $matches['id']          = is_numeric($params[1]) ? $params[1] : null;
                $matches['offset']      = is_numeric($params[2]) ? $params[2] : null;
                $matches['limit']       = is_numeric($params[3]) ? $params[3] : null;                
            } else if ('register' === $first) {
                $matches['controller']  = 'clientRegister';
                $matches['action']      = 'register';
            } else if ('c' === $first) {
                if($second) {
                    if(is_numeric($second)) {
                        $matches['id']          = is_numeric($second) ? $second : null;
                        $matches['controller']  = 'clientCategory';
                        $matches['action']      = 'category';
                    } else if(strpos($second, ".json")) {
                        $c_id = str_replace(".json", "", $second);
                        if(is_numeric($c_id)) {
                            $matches['id']          = $c_id;
                            $matches['controller']  = 'clientCategory';
                            $matches['action']      = 'categoryJson';
                        } else {
                            return null;
                        }
                    } else {
                        return null;
                    }
                } else {
                    $matches['controller']  = 'clientCategory';
                    $matches['action']      = 'categoryList'; 
                }
            } else if ('t' === $first) {
                if($second) {
                    if(is_numeric($second)) {
                        $matches['id']          = is_numeric($second) ? $second : null;
                        $matches['controller']  = 'clientTopic';
                        $matches['action']      = 'topic';
                    } else if(strpos($second, ".json")) {
                        $c_id = str_replace(".json", "", $second);
                        if(is_numeric($c_id)) {
                            $matches['id']          = $c_id;
                            $matches['controller']  = 'clientTopic';
                            $matches['action']      = 'topicJson';
                        } else {
                            return null;
                        }
                    } else {
                        return null;
                    }
                } else {
                    return null;
                }
                
//                if($second) {
//                    $matches['id']      = is_numeric($params[1]) ? $params[1] : null;
//                    if($matches['id']) {
//                        $matches['controller']  = 'clientTopic';
//                        $matches['action']      = 'topic';
//                    } else {
//                        return null;
//                    }
//                } else {
//                    return null;
//                }
            } else if ('u' === $first) {
                if($second) {
                    if(is_numeric($second)) {
                        $matches['id']          = is_numeric($second) ? $second : null;
                        $matches['controller']  = 'clientUser';
                        $matches['action']      = 'user';
                    } else if(strpos($second, ".json")) {
                        $c_id = str_replace(".json", "", $second);
                        if(is_numeric($c_id)) {
                            $matches['id']          = $c_id;
                            $matches['controller']  = 'clientUser';
                            $matches['action']      = 'userJson';
                        } else {
                            return null;
                        }
                    } else {
                        return null;
                    }
                } else {
                    return null;
                }
            } else if ('c.json' === $first) {
                $matches['controller']  = 'clientCategory';
                $matches['action']      = 'categoryListJson';
            } else {
                return null;
            }
            
            
            
            
//            if ('category' === $first) {
//                if ($fourth) {
//                    if ($request->isGet()
//                            && is_numeric($second) 
//                            && is_numeric($fourth) 
//                            && is_numeric($third)) {
//                        $matches['id'] = $second;
//                        $matches['offset'] = $third;
//                        $matches['limit'] = $fourth;
//                        $matches['controller'] = 'category';
//                        $matches['action']     = 'getTopics';
//                    } else {
//                        return null;
//                    }
//                } else if ($second) {
//                    if ($request->isGet() && is_numeric($second)) {
//                        $matches['id'] = $second;
//                        $matches['controller'] = 'category';
//                        $matches['action']     = 'getCategoryInfo';
//                        unset($params[0]);
//                    } else {
//                       return null;
//                    }
//                } else if (!$second) {
//                    if ($request->isGet()) {
//                        $matches['controller'] = 'category';
//                        $matches['action']     = 'allCategories';
//                    }
//                }
//            } else if ('topic' === $first) {
//                if ($second) {
//                    if (is_numeric($second)) {
//                        if ($method == 'GET') {
//                            $matches['id'] = $second;
//                            $matches['controller'] = 'topic';
//                            $matches['action']     = 'getTopic';
//                        } else if ($method == 'PUT') {
//                            $matches['id'] = $second;
//                            $matches['controller'] = 'topic';
//                            $matches['action']     = 'updateTopic';
//                        } else if ($method == 'DELETE') {
//                            $matches['id'] = $second;
//                            $matches['controller'] = 'topic';
//                            $matches['action']     = 'deleteTopic';
//                        } else {
//                            return null;
//                        }
//                    } else {
//                        return null;
//                    }
//                    
//                } else {
//                    if ($method == 'POST') {
//                        $matches['controller'] = 'topic';
//                        $matches['action']     = 'createTopic';
//                    } else {
//                        return null;
//                    }
//                }
//            } else if ('user' === $first) {
//                $matches['controller'] = 'user';
//                $matches['action'] = null;
//            } else {
//                return null;
//            }
            
            //if (!empty($params)) {
            //    return null;
            //}
        }
//        d(array_merge($this->defaults, $matches));
        return new RouteMatch(array_merge($this->defaults, $matches), $pathLength);
    }

    /**
     * assemble(): Defined by Route interface.
     *
     * @see    Route::assemble()
     * @param  array $params
     * @param  array $options
     * @return mixed
     */
    public function assemble(array $params = array(), array $options = array())
    {
        $mergedParams = array_merge($this->defaults, $params);
        if (!$mergedParams) {
            return '';
        }
        
        $controller = $mergedParams['controller'];
        $action     = $mergedParams['action'];
        $url        = '';
        if ('login' === $controller) {
            if ('login' === $action) {
                $url = 'login';
            } elseif ('logout' === $action) {
                $url = 'logout';
            } else {
                return '';
            }
        } elseif ('register' === $controller) {
            $url = 'register';
        } else {
            return '';
        }

        return $this->paramDelimiter . $url;
    }
}
