<?php
namespace rbwebdesigns\blogcms;
use rbwebdesigns\core\AppSecurity;
use rbwebdesigns\core\Sanitize;
use rbwebdesigns\core\DateFormatter;

/**
 * /app/controller/blog_controller.inc.php
 *  
 * The controller acts as the intermediatory between the
 * model (database) and the view. Any requests to the model are sent from
 * here rather than directly from the view.
 *
 * The content generated here is then passed through the template. Any
 * pages which are expected to return content will be passed the parameters
 * $request and $response where:
 * 
 * @author Ricky Bertram <ricky@rbwebdesigns.co.uk>
 */
class BlogController extends GenericController
{
    /**
     * @var \rbwebdesigns\blogcms\model\Blogs
     */
    protected $modelBlogs;
    /**
     * @var \rbwebdesigns\blogcms\model\Posts
     */
    protected $modelPosts;
    /**
     * @var \rbwebdesigns\blogcms\model\Contributors
     */
    protected $modelContributors;
    /**
     * @var \rbwebdesigns\blogcms\model\Comments
     */
    protected $modelComments;
    /**
     * @var \rbwebdesigns\blogcms\model\AccountFactory
     */
    protected $modelUsers;
    /**
     * @var \rbwebdesigns\core\Request
     */
    protected $request;
    /**
     * @var \rbwebdesigns\blogcms\BlogCMSResponse
     */
    protected $response;

    /**
     * Create blog controller instance
     */
    public function __construct()
    {
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\model\Blogs');
        $this->modelContributors = BlogCMS::model('\rbwebdesigns\blogcms\Contributors\model\Contributors');
        $this->modelPermissions = BlogCMS::model('\rbwebdesigns\blogcms\Contributors\model\Permissions');
        $this->modelContributorGroups = BlogCMS::model('\rbwebdesigns\blogcms\Contributors\model\ContributorGroups');
        $this->modelPosts = BlogCMS::model('\rbwebdesigns\blogcms\model\Posts');
        $this->modelComments = BlogCMS::model('\rbwebdesigns\blogcms\model\Comments');
        $this->modelUsers = BlogCMS::model('\rbwebdesigns\blogcms\UserAccounts\model\UserAccounts');
        $this->modelActivityLog = BlogCMS::model('\rbwebdesigns\blogcms\EventLogger\model\EventLogger');

        $this->request = BlogCMS::request();
        $this->response = BlogCMS::response();
    }

    public function defaultAction()
    {
        return $this->home();
    }
    
    /**
     * View the blog cms main dashboard which shows all blogs that the user contributes to
     */
    public function home()
    {
        $user = BlogCMS::session()->currentUser;
        $blogs = $this->modelContributors->getContributedBlogs($user['id']);
        
        // Add in extra information
        foreach($blogs as $key => $blog) {
            // The users who can contribute to this blog
            $blogs[$key]['contributors'] = $this->modelContributors->getBlogContributors($blog['id']);
            
            // The lastest post for this blog
            $blogs[$key]['latestpost'] = $this->modelPosts->getLatestPost($blog['id']);
            
            // Format the lastest post date for this blog
            if(gettype($blogs[$key]['latestpost']) == 'array') {
                $formatteddate = DateFormatter::formatFriendlyTime($blogs[$key]['latestpost']['timestamp']);
                $blogs[$key]['latestpost']['timestamp'] = 'Last posted: '.$formatteddate;
            }
            else {
                $lastposted = 'Currently Nothing Posted!';
            }

            // Get all menu items
            $blogActions = new Menu('bloglist');
            BlogCMS::runHook('onGenerateMenu', ['id' => 'bloglist', 'menu' => &$blogActions]);
            $blogs[$key]['actions'] = $blogActions;
        }
        
        BlogCMS::$activeMenuLink = 'dashboard';

        // Add to template
        $this->response->setVar('blogs', $blogs);
        
        // Get the current users favourite blogs
        // $arrayFavoriteBlogs = $this->modelBlogs->getAllFavourites($user['id']);
        // $this->response->setVar('favoriteblogs', $arrayFavoriteBlogs);
        // $this->response->setVar('recentposts', $this->modelPosts->getRecentPosts($arrayFavoriteBlogs, 7));
        
        $this->response->addScript('/js/showUserCard.js');
        $this->response->setTitle('My Blogs');
        $this->response->write('index.tpl');
    }
    
    /**
     * View the new blog form
     */
    public function create()
    {
        if ($this->request->method() == 'POST') return $this->runCreateBlog();

        $this->response->setTitle('Create New Blog');
        $this->response->write('newblog.tpl');
    }
        
    /**
     * View overview/ summary of a single blog
     */
    public function overview()
    {
        $blogID = $this->request->getUrlParameter(1);
        $currentUser = BlogCMS::session()->currentUser;

        // Validation
        if(strlen($blogID) == 0) {
            $this->response->redirect('/cms');
        }
        elseif(!$this->modelContributors->isBlogContributor($currentUser['id'], $blogID)) {
            $this->response->redirect('/cms', 'You do not contribute to this blog', 'error');
        }

        $blog = $this->modelBlogs->getBlogById($blogID);
        $this->response->setVar('blog', $blog);
        
        // Get latest 5 posts
        $this->response->setVar('posts', $this->modelPosts->getPostsByBlog($blogID, 1, 5, 1, 1));
        $this->response->setVar('activitylog', $this->modelActivityLog->byBlog($blogID));

        $counts = [];
        BlogCMS::runHook('dashboardCounts', ['blogID' => $blogID, 'counts' => &$counts]);

        // Get count statistics
        $this->response->setVar('counts', array_merge($counts, [
            'posts' => $this->modelPosts->countPostsOnBlog($blogID, true),
            'contributors' => $this->modelContributors->getCount(array('blog_id' => $blogID)),
            'totalviews' => $this->modelPosts->countTotalPostViews($blogID),
        ]));
        
        $panels = [];
        BlogCMS::runHook('dashboardPanels', ['blog' => $blog, 'panels' => &$panels]);
        $this->response->setVar('panels', $panels);

        BlogCMS::$activeMenuLink = 'overview';
        $this->response->setTitle('Dashboard - '.$blog['name']);
        $this->response->write('overview.tpl');
    }
    
    /**
     * Create a new blog
     * 
     * @todo add blog limit into config
     */
    public function runCreateBlog()
    {
        $currentUser = BlogCMS::session()->currentUser;

        if(!IS_DEVELOPMENT && $this->modelBlogs->countBlogsByUser($currentUser) > 4) {
            $this->response->redirect('/cms', 'Unable to Continue - Maximum number of blogs exceeded!', 'Error');
        }
        else {
            $newblogkey = $this->modelBlogs->createBlog($this->request->getString('fld_blogname'), $this->request->getString('fld_blogdesc'));

            if (!$newblogkey) {
                $this->response->redirect('/cms', 'Error creating blog please try again later', 'error');
            }

            if (!$this->modelContributorGroups->createDefaultGroups($newblogkey)) {
                $this->response->redirect('/cms', 'Error creating contributor groups please try again later', 'error');
            }

            $adminGroup = $this->modelContributorGroups->get(['id'], ['blog_id' => $newblogkey, 'name' => 'Admin'], '', '', false);

            if (!$adminGroup) die('No admin found' . $newblogkey);

            if (!$this->modelContributors->addBlogContributor($currentUser['id'], $newblogkey, $adminGroup['id'])) {
                $this->response->redirect('/cms', 'Error adding to contributor please try again later', 'error');
            }

            $this->response->redirect('/cms/blog/overview/' . $newblogkey, 'Blog created', 'Success');
        }
    }

    /**
     * Confirm delete page
     */
    public function delete()
    {
        $currentUser = BlogCMS::session()->currentUser;
        $blogID = $this->request->getUrlParameter(1);

        if (!$blog = $this->modelBlogs->getBlogById($blogID)) {
            $this->response->redirect('/cms', 'Blog not found', 'error');
        }

        // Only the owner can delete the blog
        if ($blog['user_id'] != $currentUser['id']) {
            $this->response->redirect('/cms', 'Access denied', 'error');
        }

        if ($this->request->method() == 'POST') return $this->runDeleteBlog($blog);

        $this->response->setTitle('Delete New Blog');
        $this->response->write('deleteblog.tpl');
    }

    /**
     * Action the delete blog
     */
    protected function runDeleteBlog($blog)
    {
        // Delete posts
        $this->modelContributors->delete(['blog_id' => $blog['id']]);
        $this->modelContributorGroups->delete(['blog_id' => $blog['id']]);
        $this->modelPosts->delete(['blog_id' => $blog['id']]);
        $this->modelComments->delete(['blog_id' => $blog['id']]);
        $this->modelBlogs->delete(['id' => $blog['id']]);

        $this->deleteDir(SERVER_PATH_BLOGS . '/' . $blog['id']);

        $this->response->redirect('/cms', 'Blog deleted', 'success');

        // What's not deleted
        // postviews
        // favourites (not implemented anyhow)
    }

    /**
     * This could be moved into a core function
     * Source: https://stackoverflow.com/a/3349792
     */
    protected function deleteDir($dirPath)
    {
        if (!is_dir($dirPath)) {
            throw new InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }
        $files = glob($dirPath . '*', GLOB_MARK);
        foreach ($files as $file) {
            if (is_dir($file)) {
                self::deleteDir($file);
            } else {
                unlink($file);
            }
        }
        rmdir($dirPath);
    }

}
