<?php

namespace rbwebdesigns\blogcms;

class GenericController {
    
    protected $view;
    protected $request;
    protected $response;
    
    public function __construct() {
        
    }

    public function defaultAction()
    {
        
    }
    
    /**
     * Side menu content
     */
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

                            
                            