<?php
/* << replace >>*/

namespace Module\Discourse\Lib;

use Pi;
use Pi\Mvc\Controller\ActionController;
use Zend\Http\Request as HttpRequest;
use Zend\Mvc\Exception;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;

/**
 * Abstract RESTful controller for pi-discourse module
 *
 * @see Zend\Mvc\AbstractRestfulController
 */
abstract class DiscourseRestfulController extends ActionController
{
    /**
     * @var string
     */
    protected $eventIdentifier = __CLASS__;

    /**
     * Set theme to discourse
     */
    public function __construct() {
        Pi::service('theme')->setTheme('discourse');
    }
    
    /**
     * Return list of resources
     *
     * @return mixed
     */
    public function getList()
    {}

    /**
     * Return single resource
     *
     * @param  mixed $id
     * @return mixed
     */
    public function get($id)
    {}

    /**
     * Create a new resource
     *
     * @param  mixed $data
     * @return mixed
     */
    public function create($data)
    {}

    /**
     * Update an existing resource
     *
     * @param  mixed $id
     * @param  mixed $data
     * @return mixed
     */
    public function update($id, $data)
    {}

    /**
     * Delete an existing resource
     *
     * @param  mixed $id
     * @return mixed
     */
    public function delete($id)
    {}
    
    /**
     * Return resources with filter, offset, limit
     *
     * @param  mixed $id
     * @return mixed
     */
    public function getMulti($filterId, $offset, $limit)
    {}

    /**
     * Basic functionality for when a page is not available
     *
     * @return array
     */
    public function notFoundAction()
    {
        $this->response->setStatusCode(404);

        return array('content' => 'Page not found');
    }

    /**
     * Dispatch a request
     *
     * If the route match includes an "action" key, then this acts basically like
     * a standard action controller. Otherwise, it introspects the HTTP method
     * to determine how to handle the request, and which method to delegate to.
     *
     * @events dispatch.pre, dispatch.post
     * @param  Request $request
     * @param  null|Response $response
     * @return mixed|Response
     * @throws Exception\InvalidArgumentException
     */
    public function dispatch(Request $request, Response $response = null)
    {
        if (!$request instanceof HttpRequest) {
            throw new Exception\InvalidArgumentException('Expected an HTTP request');
        }

        return parent::dispatch($request, $response);
    }

    /**
     * Handle the request
     *
     * @param  MvcEvent $e
     * @return mixed
     * @throws Exception\DomainException if no route matches in event or invalid HTTP method
     */
    public function onDispatch(MvcEvent $e)
    {
        $return = null;
        if (!$this->skipExecute) {
            $routeMatch = $e->getRouteMatch();
            if (!$routeMatch) {
                /**
                * @todo Determine requirements for when route match is missing.
                *       Potentially allow pulling directly from request metadata?
                */
                throw new Exception\DomainException('Missing route matches; unsure how to retrieve action');
            }

            $request = $e->getRequest();
            $action  = $routeMatch->getParam('action', false);
            //$action = false;
            if ($action) {
                // Handle arbitrary methods, ending in Action
                $method = static::getMethodFromAction($action);
                if (!method_exists($this, $method)) {
                    $method = 'notFoundAction';
                }
                $return = $this->$method();
            } else {
                // RESTful methods
                switch (strtolower($request->getMethod())) {
                    case 'get':
                        $id     = $routeMatch->getParam('id');
                        $offset = $routeMatch->getParam('offset');
                        $limit  = $routeMatch->getParam('limit');
                        if (null != $id && null != $offset && null != $limit) {
                            $action = 'getMulti';
                            $return = $this->getMulti($id, $offset, $limit);
                            break;
                        }
                        if (null != $id && null != $offset && null == $limit) {
                            throw new Exception\DomainException('Invalid HTTP method!');
                            break;
                        }
                        if (null != $id) {
                            $action = 'get';
                            $return = $this->get($id);
                            break;
                        }
//                        if (null == $id = $request->getQuery()->get('id')) {
//                            $action = 'get';
//                            $return = $this->get($id);
//                            break;
//                        }
                        $action = 'getList';
                        $return = $this->getList();
                        break;
                    case 'post':
                        $action = 'create';
                        $return = $this->processPostData($request);
                        break;
                    case 'put':
                        $action = 'update';
                        $return = $this->processPutData($request, $routeMatch);
                        break;
                    case 'delete':
                        if (null === $id = $routeMatch->getParam('id')) {
                            if (!($id = $request->getQuery()->get('id', false))) {
                                throw new Exception\DomainException('Missing identifier');
                            }
                        }
                        $action = 'delete';
                        $return = $this->delete($id);
                        break;
                    default:
                        throw new Exception\DomainException('Invalid HTTP method!');
                }

                $routeMatch->setParam('action', $action);
            }
        }

        // Emit post-dispatch signal, passing:
        // - return from method, request, response
        // If a listener returns a response object, return it immediately
        //$e->setResult($return);

        // Collect directly returned scalar content

//        if (null !== $return && is_scalar($return)) {
//            $this->view()->setTemplate(false);
//            $this->view()->assign('content', $return);
//            $return = null;
//        }
//        if (null === $return && $this->view()->hasViewModel()) {
//            $return = $this->view()->getViewModel();
//            $e->setResult($return);
//        }
        
        $e->setResult($return);
        return $return;
    }

    /**
     * Process post data and call create
     *
     * @param Request $request
     * @return mixed
     */
    public function processPostData(Request $request)
    {
        return $this->create($request->getPost()->toArray());
    }

    /**
     * Process put data and call update
     *
     * @param Request $request
     * @param $routeMatch
     * @return mixed
     * @throws Exception\DomainException
     */
    public function processPutData(Request $request, $routeMatch)
    {
        if (null === $id = $routeMatch->getParam('id')) {
            if (!($id = $request->getQuery()->get('id', false))) {
                throw new Exception\DomainException('Missing identifier');
            }
        }
        $content = $request->getContent();
        parse_str($content, $parsedParams);

        return $this->update($id, $parsedParams);
    }
}
