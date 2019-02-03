<?php
namespace rbwebdesigns\blogcms\Blog\controller;

use rbwebdesigns\blogcms\GenericController;
use rbwebdesigns\blogcms\BlogCMS;
use rbwebdesigns\core\AppSecurity;
use rbwebdesigns\core\Sanitize;
use rbwebdesigns\core\DateFormatter;
use rbwebdesigns\blogcms\Menu;

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
class Blogs extends GenericController
{
    /**
     * @var \rbwebdesigns\blogcms\Blog\model\Blogs
     */
    protected $modelBlogs;
    /**
     * @var \rbwebdesigns\blogcms\BlogPosts\model\Posts
     */
    protected $modelPosts;
    /**
     * @var \rbwebdesigns\blogcms\Contributors\model\Contributors
     */
    protected $modelContributors;
    /**
     * @var \rbwebdesigns\blogcms\PostComments\model\Comments
     */
    protected $modelComments;
    /**
     * @var \rbwebdesigns\blogcms\UserAccounts\model\UserAccounts
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
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\Blog\model\Blogs');
        $this->modelContributors = BlogCMS::model('\rbwebdesigns\blogcms\Contributors\model\Contributors');
        $this->modelPermissions = BlogCMS::model('\rbwebdesigns\blogcms\Contributors\model\Permissions');
        $this->modelContributorGroups = BlogCMS::model('\rbwebdesigns\blogcms\Contributors\model\ContributorGroups');
        $this->modelPosts = BlogCMS::model('\rbwebdesigns\blogcms\BlogPosts\model\Posts');
        $this->modelComments = BlogCMS::model('\rbwebdesigns\blogcms\PostComments\model\Comments');
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
            // Get all menu items
            $blogActions = new Menu('bloglist');
            BlogCMS::runHook('onGenerateMenu', ['id' => 'bloglist', 'menu' => &$blogActions, 'blog' => $blog]);
            $blogs[$key]->actions = $blogActions->getLinks();
        }
        
        BlogCMS::$activeMenuLink = '/cms/blog';

        // Add to template
        $this->response->setVar('blogs', $blogs);
        $this->response->addScript('/js/showUserCard.js');
        $this->response->setTitle('My Blogs');
        $this->response->write('index.tpl', 'Blog');
    }
    
    /**
     * View the new blog form
     */
    public function create()
    {
        if ($this->request->method() == 'POST') return $this->runCreateBlog();

        $this->response->setTitle('Create New Blog');
        $this->response->write('newblog.tpl', 'Blog');
    }
        
    /**
     * View overview/ summary of a single blog
     */
    public function overview()
    {
        $blogID = $this->request->getUrlParameter(1);
        $blog = $this->modelBlogs->getBlogById($blogID);

        // Validation
        if (!$blog) {
            $this->response->redirect('/cms');
        }
        elseif (!$blog->isContributor()) {
            $this->response->redirect('/cms', 'You do not contribute to this blog', 'error');
        }

        $counts = [];
        BlogCMS::runHook('dashboardCounts', ['blogID' => $blogID, 'counts' => &$counts]);

        $modelPostViews = BlogCMS::model('\rbwebdesigns\blogcms\BlogPosts\model\PostViews');

        // Get count statistics
        $this->response->setVar('counts', array_merge($counts, [
            'posts' => $this->modelPosts->countPostsOnBlog($blogID, true),
            'contributors' => $this->modelContributors->getCount(array('blog_id' => $blogID)),
            'totalviews' => $modelPostViews->getTotalPostViewsByBlog($blogID),
        ]));
        
        $panels = [];
        BlogCMS::runHook('dashboardPanels', ['blog' => $blog, 'panels' => &$panels]);
        $this->response->setVar('panels', $panels);
        $this->response->setVar('blog', $blog);
        $this->response->setVar('posts', $this->modelPosts->getPostsByBlog($blogID, 1, 5, 1, 1));
        // $this->response->setVar('activitylog', $this->modelActivityLog->byBlog($blogID));

        BlogCMS::$activeMenuLink = '/cms/blog/overview/'. $blog->id;
        $this->response->setTitle('Dashboard - '. $blog->name);
        $this->response->write('overview.tpl', 'Blog');
    }
    
    /**
     * Create a new blog
     * 
     * @todo add blog limit into config
     */
    public function runCreateBlog()
    {
        $currentUser = BlogCMS::session()->currentUser;

        // Check we've got the root.inc.php file under blogdata root
        if (!file_exists(SERVER_PATH_BLOGS .'/root.inc.php')) {
            // Replace contents
            $fileContents = file_get_contents(SERVER_ROOT.'/app/root.default.php');

            $fileContents = str_replace("{SERVER_ROOT}", BlogCMS::config()['environment']['root_directory'], $fileContents);
            $fileContents = str_replace("{CMS_DOMAIN}", BlogCMS::config()['environment']['canonical_domain'], $fileContents);

            // Copy file
            $copy = file_put_contents(SERVER_PATH_BLOGS.'/root.inc.php', $fileContents);
            if (!$copy) die("Failed to create root file, please check directory permissions for: ".SERVER_PATH_BLOGS);
        }

        // Hard limit of 4 - need to add option to configuration
        // @todo all this to be configured!
        if(!IS_DEVELOPMENT && $this->modelBlogs->countBlogsByUser($currentUser) > 4) {
            $this->response->redirect('/cms', 'Unable to Continue - Maximum number of blogs exceeded!', 'Error');
            return;
        }
        
        // Create blog db entry
        $newblogkey = $this->modelBlogs->createBlog($this->request->getString('fld_blogname'), $this->request->getString('fld_blogdesc'));

        if (!$newblogkey) {
            $this->response->redirect('/cms', 'Error creating blog please try again later', 'error');
            return;
        }

        // Create admin groups
        // @todo get this function to return the admin group ID!
        if (!$this->modelContributorGroups->createDefaultGroups($newblogkey)) {
            $this->response->redirect('/cms', 'Error creating contributor groups please try again later', 'error');
            return;
        }

        $adminGroup = $this->modelContributorGroups->get(['id'], ['blog_id' => $newblogkey, 'name' => 'Admin'], '', '', false);

        if (!$adminGroup) die('No admin found' . $newblogkey);

        // Add the user as contributor
        if (!$this->modelContributors->addBlogContributor($currentUser['id'], $newblogkey, $adminGroup->id)) {
            $this->response->redirect('/cms', 'Error adding to contributor please try again later', 'error');
            return;
        }

        $this->response->redirect('/cms/blog/overview/' . $newblogkey, 'Blog created', 'Success');
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
        if ($blog->user_id != $currentUser['id']) {
            $this->response->redirect('/cms', 'Access denied', 'error');
        }

        if ($this->request->method() == 'POST') return $this->runDeleteBlog($blog);

        $this->response->setTitle('Delete blog');
        $this->response->write('deleteblog.tpl');
    }

    /**
     * Action the delete blog
     */
    protected function runDeleteBlog($blog)
    {
        // Delete posts
        $this->modelContributors->delete(['blog_id' => $blog->id]);
        $this->modelContributorGroups->delete(['blog_id' => $blog->id]);
        $this->modelPosts->delete(['blog_id' => $blog->id]);
        $this->modelComments->delete(['blog_id' => $blog->id]);
        $this->modelBlogs->delete(['id' => $blog->id]);

        $this->deleteDir(SERVER_PATH_BLOGS . '/' . $blog->id);

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
