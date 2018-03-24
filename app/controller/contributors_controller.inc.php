<?php
namespace rbwebdesigns\blogcms;

use rbwebdesigns\core\Sanitize;

/**
    ContributorsController
    
    add(blog)
    actionAdd(blogid)
**/

class ContributorsController extends GenericController
{
    // Models
    protected $modelUsers;
    protected $modelBlogs;
    protected $modelPosts;
    protected $model;
    
    public function __construct()
    {
        $this->modelUsers =  BlogCMS::model('\rbwebdesigns\blogcms\model\UserFactory');
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\model\Blogs');
        $this->modelPosts = BlogCMS::model('\rbwebdesigns\blogcms\model\Posts');
        $this->model = BlogCMS::model('\rbwebdesigns\blogcms\model\Contributors');
        
        BlogCMS::$activeMenuLink = 'users';
    }
        
    /**
        Route all requests to the correct functions
        examples
        /blog_cms/contributors/1734978340
        /blog_cms/contributors/1734978340/add
    **/
    public function route($params)
    {
        // Handle Arguments
        $blogid = Sanitize::int($params[0]);
        
        // Check we have permission to perform action
        if(!$this->modelContributors->isBlogContributor($blogid, $_SESSION['userid'], 'all')) return $this->throwAccessDenied();
        
        $blog = $this->modelBlogs->getBlogById($blogid);
        $action = array_key_exists(1, $params) ? strtolower($params[1]) : 'manage';
        
        switch($action):
        
            case "add":
                // Check if form submitted
                if(array_key_exists(2, $params) && strtolower($params[2]) == "submit")
                {
                    $this->actionAdd($blogid);
                }
                
                // View 'add contributor page'
                $this->view->setPageTitle('Add New Contributor - '.$blog['name']);
                $this->view->setVar('blog', $blog);
                // $this->view->setVar('friends', $this->modelUsers->expandedFriendsList($_SESSION['userid']));
                $this->view->render('contributors/new.tpl');
                break;
        
            case "delete":
                if(isset($_POST['fld_UserID'])) $this->actionDelete($_POST['fld_UserID'], $blogid);
                else $this->throwAccessDenied();
                break;
        
            case "update":
                if(isset($_POST['fld_UserID']) && isset($_POST['fld_Permission'])) $this->changePermissions($_POST['fld_UserID'], $blogid, $_POST['fld_Permission']);
                else $this->throwAccessDenied();
                break;
                
            default:
            $this->manage($blog);
            break;
        
        endswitch;
    }
    
    
    /**
     * View the manage contributors page
     */
    public function manage(&$request, &$response)
    {
        $blogID = $request->getUrlParameter(1);
        $blog = $this->modelBlogs->getBlogById($blogID);

        $currentUser = BlogCMS::session()->currentUser;

        if (!$this->model->isBlogContributor($blog['id'], $currentUser['id'], 'all')) {
            $response->redirect('/', 'Access denied', '');
        }

        $response->setVar('contributors', $this->model->getBlogContributors($blog['id']));
        $response->setVar('postcounts', $this->modelPosts->countPostsByUser($blog['id'])); // Get the number of post each contributor has made
        $response->setVar('blog', $blog);
        $response->setTitle('Manage Blog Contributors - '.$blog['name']);
        $response->write('contributors/manage.tpl');
    }    
    
    /**
     * Add a contributor to the database
     */
    public function actionAdd($blogid)
    {
        if($_POST['fld_contributor'] == 0)
        {
            setSystemMessage("Failed to add contributor - no user selected!");
        }
        else
        {
            $this->modelContributors->addBlogContributor($_POST['fld_contributor'], $_POST['fld_privileges'], $blogid);
            setSystemMessage(ITEM_CREATED, "Success");
        }
        redirect('/contributors/'.$blogid);
    }
    
    /**
     * Update the permission for a contributor
     */
    public function changePermissions($userid, $blogid, $permissions)
    {
        if($this->modelContributors->changePermissions($userid, $blogid, $permissions)) setSystemMessage(ITEM_UPDATED, "Success");
        else setSystemMessage("Unable to update user", "Error");
        redirect('/contributors/'.$blogid);
    }
    
    /**
     * Remove a contributor
    **/
    public function actionDelete($userid, $blogid)
    {
        // Check this isn't the current user?!
        if($userid == USER_ID) die('Cannot remove yourself from the blog');
        
        // We are letting the model handle validation
        if($this->modelContributors->delete(array('user_id' => $userid, 'blog_id' => $blogid))) setSystemMessage(ITEM_DELETED, "Success");       
        else setSystemMessage("Unable to delete user", "Error");
        redirect('/contributors/'.$blogid);
    }
    
}
