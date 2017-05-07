<?php
namespace rbwebdesigns\blogcms;

class ApiController extends GenericController
{
    private $db;
    protected $view;
    
    public function __construct($db, $view, $params)
    {
        $this->db = $db;
        $this->view = $view;
        
        $this->route($params);
    }
    
    public function route($params)
    {
        $method = ($params == false) ? null : sanitize_string($params[0]);
        
        // Set up models here
        $modelBlogs = new ClsBlog($this->db);
        $modelPosts = new ClsPost($this->db);
        
        switch($method)
        {
            case 'posts':
                require SERVER_ROOT.'/app/api/posts.php';
                break;
        }
        
        return;
    }
    
}
?>