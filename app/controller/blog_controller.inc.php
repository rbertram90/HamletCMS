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
 * $request \rbwebdesigns\core\Request
 * $response \rbwebdesigns\core\Response
 * 
 * @author R Bertram <ricky@rbwebdesigns.co.uk>
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

    protected $request;
    protected $response;


    public function __construct()
    {
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\model\Blogs');
        $this->modelContributors = BlogCMS::model('\rbwebdesigns\blogcms\model\Contributors');
        $this->modelContributorGroups = BlogCMS::model('\rbwebdesigns\blogcms\model\ContributorGroups');
        $this->modelPosts = BlogCMS::model('\rbwebdesigns\blogcms\model\Posts');
        $this->modelComments = BlogCMS::model('\rbwebdesigns\blogcms\model\Comments');
        $this->modelUsers = BlogCMS::model('\rbwebdesigns\blogcms\model\AccountFactory');

        $this->request = BlogCMS::request();
        $this->response = BlogCMS::response();
    }

    public function logout($params)
    {
        session_start();
        session_destroy();

        // Navigate to homepage
        header("location: /");
    }

    public function defaultAction(&$request, &$response)
    {
        return $this->home($request, $response);
    }
    
    /**
     * View the blog cms main dashboard which shows all blogs that the user contributes to
     */
    public function home(&$request, &$response)
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
        }
        
        BlogCMS::$activeMenuLink = 'dashboard';

        // Add to template
        $response->setVar('blogs', $blogs);
        
        // Get the current users favourite blogs
        $arrayFavoriteBlogs = $this->modelBlogs->getAllFavourites($user['id']);
        $response->setVar('favoriteblogs', $arrayFavoriteBlogs);
        $response->setVar('recentposts', $this->modelPosts->getRecentPosts($arrayFavoriteBlogs, 7));
        
        $response->addScript('/js/showUserCard');
        $response->setTitle('My Blogs');
        $response->write('index.tpl');
    }
    
    /**
     * View the new blog form
     */
    public function create(&$request, &$response)
    {
        if ($request->method() == 'POST') return $this->runCreateBlog($request, $response);

        $response->setTitle('Create New Blog');
        $response->write('newblog.tpl');
    }

    /**
     * Explore Pages
    **/
    public function explore($params)
    {
        if(strlen($params[0]) == 0) $params[0] = 'blogsbyletter';
        
        switch(Sanitize::string($params[0]))
        {
            case 'popular':
                // Get most favourited blogs
                $this->view->setVar('topblogs', $this->modelBlogs->getTopFavourites());
                $this->view->setPageTitle('Explore Blogs - Top Favourites');
                $this->view->render('explore/popular.tpl');
                break;
                
            case 'category':
                $category = 'general';
                if(array_key_exists(1, $params)) $category = Sanitize::string($params[1]);
                
                $this->view->setVar('currentcategory', $category);
                $this->view->setVar('categories', $GLOBALS['config']['blogcategories']);
                $this->view->setVar('blogs', $this->modelBlogs->getByCategory($category));
                $this->view->setPageTitle('Explore Blogs by Category');
                $this->view->render('explore/category.tpl');
                break;
        }
    }
    
    /**
     * View the Documentation Page (Admin Link Only)
     */
    public function viewDocs($params)
    {
        $this->view->setPageTitle('Developer Documentation');
        $this->view->addStylesheet('/css/docs');
        $this->view->render('documentation.tpl');
    }
    
    /**
     * View overview/ summary of a single blog
     */
    public function overview(&$request, &$response)
    {
        $blogID = $request->getUrlParameter(1);
        $currentUser = BlogCMS::session()->currentUser;

        // Validation
        if(strlen($blogID) == 0) {
            $response->redirect('/cms');
        }
        elseif(!$this->modelContributors->isBlogContributor($blogID, $currentUser['id'])) {
            $response->redirect('/cms', 'You do not contribute to this blog', 'error');
        }

        $blog = $this->modelBlogs->getBlogById($blogID);
        $response->setVar('blog', $blog);
        
        // Get latest 5 comments
        $latestcomments = $this->modelComments->getCommentsByBlog($blogID, 5);
        
        // Get comment-ers usernames
        foreach($latestcomments as $key => $comment) {
            $user = $this->modelUsers->getById($comment['user_id']);
            $username = $comment['user_id'] == $currentUser['id'] ? "You" : $user['username'];
            $latestcomments[$key]['userid'] = $comment['user_id'];
            $latestcomments[$key]['name'] = $username;
        }
        $response->setVar('comments', $latestcomments);
        
        // Get latest 5 posts
        $response->setVar('posts', $this->modelPosts->getPostsByBlog($blogID, 1, 5, 1, 1));
        
        // Get count statistics
        $response->setVar('counts', array(
            'posts' => $this->modelPosts->countPostsOnBlog($blogID, true),
            'comments' => $this->modelComments->getCount(array('blog_id' => $blogID)),
            'contributors' => $this->modelContributors->getCount(array('blog_id' => $blogID)),
            'totalviews' => $this->modelPosts->countTotalPostViews($blogID)
        ));
        
        BlogCMS::$activeMenuLink = 'overview';
        $response->setTitle('Dashboard - '.$blog['name']);
        $response->write('overview.tpl');
    }
            
    /******************************************************************
        POST - Blogs
    ******************************************************************/
    
    /**
     * Create a new blog
     * 
     * @todo add blog limit into config
     */
    public function runCreateBlog(&$request, &$response)
    {
        $currentUser = BlogCMS::session()->currentUser;

        if(!IS_DEVELOPMENT && $this->modelBlogs->countBlogsByUser($currentUser) > 4) {
            $response->redirect('/cms', 'Unable to Continue - Maximum number of blogs exceeded!', 'Error');
        }
        else {
            $newblogkey = $this->modelBlogs->createBlog($request->getString('fld_blogname'), $request->getString('fld_blogdesc'));
            // var_dump($newblogkey);
            // die();
            if (!$newblogkey) {
                $response->redirect('/cms', 'Error creating blog please try again later', 'error');
            }

            if (!$this->modelContributorGroups->createDefaultGroups($newblogkey)) {
                $response->redirect('/cms', 'Error creating contributor groups please try again later', 'error');
            }

            $adminGroup = $this->modelContributorGroups->get(['id'], ['blog_id' => $newblogkey, 'name' => 'Admin'], '', '', false);

            if (!$adminGroup) die('No admin found' . $newblogkey);

            if (!$this->modelContributors->addBlogContributor($currentUser['id'], $newblogkey, $adminGroup['id'])) {
                $response->redirect('/cms', 'Error adding to contributor please try again later', 'error');
            }

            $response->redirect('/cms/blog/overview/' . $newblogkey, 'Blog created', 'Success');
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
    protected function deleteDir($dirPath) {
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
