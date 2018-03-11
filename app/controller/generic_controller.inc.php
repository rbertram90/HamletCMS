<?php

namespace rbwebdesigns\blogcms;

class GenericController {
    
    protected $view;
    
    public function __construct($cms_db, $view) {
        $this->view = $view;
    }

    public function defaultAction(&$request, &$response)
    {
        $this->throwNotFound();
    }
    
    /**
        Throw Errors - can we include instead!
    **/
    public function throwAccessDenied() {
        redirect('/accessdenied');
        return;
    }
    
    public function throwNotFound() {
        redirect('/notfound');
        return;
    }
    
    public function notFound($params) {
        // require_once SERVER_ROOT.'/app/public/404.php';
        $this->view->setPageTitle('Page Not Found');
        $this->view->render('404.tpl');
    }
    
    public function accessDenied($params) {
        // require_once SERVER_ROOT.'/app/public/403.php';
        $this->view->setPageTitle('Access Denied');
        $this->view->render('403.tpl');
    }
    
    /**
        Side menu content
    **/
    public function getSideMenu($params, $currentpage) {
        if(gettype($params) === "array") {
            if(strlen($params[0]) === 10 && is_numeric($params[0])) {
                // guessing it is a blog id, should we be checking against the DB here?
                $blogid = $params[0];
                require_once SERVER_ROOT.'/app/view/sidemenu.php';
                return getCMSSideMenu($blogid, $currentpage);
            }
        }
        return '<li class="nolink">Actions</li><li><a href="/newblog"><img src="/resources/icons/64/doc_add.png"><span class="menuitemtext">Create a new blog</span></a></li>';
    }

}
?>
                            
                            