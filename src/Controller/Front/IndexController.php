<?php
/* << replace >>*/

namespace Module\Discourse\Controller\Front;

use Pi;
use Pi\Mvc\Controller\ActionController;
use Module\Discourse\Controller\Front\CategoryController;

class IndexController extends ActionController
{
    /**
     * A test page with a couple of API demos
     */
    public function indexAction()
    {
        // Assign multiple params
        //d('k');
        
        // Specify template, otherwise template will be set up as {controller}-{action}
        $this->view()->setTemplate('discourse-blank');
    }

    
}
