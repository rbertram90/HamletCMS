<?php
namespace rbwebdesigns\blogcms;

/**
    Note - this is not currently used - however we need to ensure that it is not
    the controller that is outputting anything so we need another layer for the view
    if we need any thing more complex than a function which just requires a
    file i don't know.
    
    This should be initalised before the contructor and passed through
    note also that the model is used here but not in the constructor???
    
    All views through this are now done using the smarty templating engine, the reason
    is to improve view readability and to seperate further the view from the controller.
**/

class View {
    
    protected $data = "";
    protected $cssFiles = array();
    protected $jsFiles = array();
    
    // Are we keeping models stored here?
    protected $modelUsers;
    
    protected $smarty;
    
    public function __construct($models)
    {
        // Models
        $this->modelUsers = $models['users'];
        
        // Initialise Smarty
        $this->smarty = new \Smarty;
        $this->smarty->template_dir = SERVER_ROOT.'/app/view/smarty/';
        
        // Add default variables
        $this->setVar('page_title', 'Default Page Title');
        $this->setVar('page_description', 'Default Page Description');
        
        // Add default stylesheet(s)
        $this->addStylesheet('/css/semantic');
        // $this->addStylesheet('/resources/css/core');
        $this->addStylesheet('/resources/css/header');
        // $this->addStylesheet('/resources/css/forms');
        $this->addStylesheet('/css/blogs_stylesheet');
        
        // Add default script(s)
        $this->addScript('/resources/js/jquery-1.8.0.min');
        $this->addScript('/js/semantic');
        $this->addScript('/resources/js/core-functions');
        $this->addScript('/resources/js/validate');
        $this->addScript('/resources/js/ajax');
        $this->addScript('/js/sidemenu');
    }
    
    /**
        render - Output the current view - this should be the final function call
        @param contenttemplate <string> path to smarty template
        @param usesmarty <boolean> true (default) to parse template with smarty, false to use
            as php file with data as described as below
        @param data <array> if usesmarty is false then custom data can be easily passed through
            to the view at this point - quite a nice little trick for cases where using smarty
            would be complex (e.g. blog designer)
    **/
    public function render($contenttemplate, $data=false)
    {
        // Either way we still use smarty for the template
        $this->setVar('serverroot', SERVER_ROOT);
        
        if(isset($_SESSION['userid']))
        {
            $this->setVar('currentuser', $this->modelUsers->getUserById($_SESSION['userid']));
        }
        
        $usesmarty = (substr($contenttemplate, -3) == 'tpl');
        
        // Override for some things which would be difficult
        // to convert to using smarty
        if($usesmarty == false)
        {
            ob_start();
            
            if(file_exists($contenttemplate))
            {
                if($data !== false)
                {
                    // Set variables passed from controller
                    foreach($data as $key => $value) $$key = $value;
                }
                
                require $contenttemplate;
            }
            else
            {
                // File passed in doesn't exist
                require SERVER_ROOT.'/app/www_root/404.php';
            }
            
            // Save current content
            $this->setVar('content', ob_get_contents());
            
            ob_end_clean();
        }
        else
        {
            ob_start();

            // Run page content
            $this->smarty->display($contenttemplate);

            // Clear all variables
            // $this->clearVars();

            // Save current content
            $this->setVar('content', ob_get_contents());

            ob_end_clean();
        }
        
        $this->setVar('stylesheets', $this->cssFiles);
        $this->setVar('jsscripts', $this->jsFiles);
        
        // Display with template
        $this->smarty->display('template.tpl');
        
        // Clear any flash message
        $_SESSION['messagetoshow'] = "";
    }
    
    public function setVar($name, $value)
    {
        // Set variable in smarty template
        $this->smarty->assign($name, $value);
    }
    
    public function clearVars()
    {
        // Clear all variables
        $this->smarty->clear_all_assign();
    }
    
    /* Special Case Setters */
    public function setPageTitle($title)
    {
        $this->setVar('page_title', $title);
    }
    
    public function setPageDescription($description)
    {
        $this->setVar('page_description', $description);
    }
    
    public function setSideMenu($menucontent) {
        $this->setVar('page_sidemenu', $menucontent);
    }
    
    /**
        @param csspath - note this should be relative to the root (client side) directory
        @return boolean - true if added false otherwise
    **/
    public function addStylesheet($csspath)
    {
        $this->cssFiles[] = $csspath;
    }
    
    /**
        @param jspath - note this should be relative to the root (client side) directory
        @return boolean - true if added false otherwise
    **/
    public function addScript($jspath)
    {
        $this->jsFiles[] = $jspath;
    }
    
    // do we need this function or do we leave it down to the template?
    private function renderHead()
    {
        // loop through stylesheets?
        // loop through js files?
        // ouput page title?
        // meta tags
    }
}
    
?>