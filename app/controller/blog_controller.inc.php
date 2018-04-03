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


    public function __construct()
    {
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\model\Blogs');
        $this->modelContributors = BlogCMS::model('\rbwebdesigns\blogcms\model\Contributors');
        $this->modelPosts = BlogCMS::model('\rbwebdesigns\blogcms\model\Posts');
        $this->modelComments = BlogCMS::model('\rbwebdesigns\blogcms\model\Comments');
        $this->modelUsers = BlogCMS::model('\rbwebdesigns\blogcms\model\AccountFactory');
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
            case 'blogsbyletter':
                $this->view->setVar('counts', $this->modelBlogs->countBlogsByLetter());
                $this->view->setVar('alphabet', array('0','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'));

                if(array_key_exists(1, $params)) {
                    // Get Target Letter
                    $currentletter = strlen($params[1]) == 1 ? Sanitize::string($params[1]) : 'A';
                    $this->view->setVar('letter', $currentletter);
                    $this->view->setVar('blogs', $this->modelBlogs->getBlogsByLetter($currentletter));
                }
                $this->view->setPageTitle('Explore Blogs - Browse Blogs By Letter');
                $this->view->render('explore/browse.tpl');
                break;

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
            $response->redirect('/');
        }
        elseif(!$this->modelContributors->isBlogContributor($blogID, $currentUser['id'])) {
            $response->redirect('/', 'You do not contribute to this blog', 'error');
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
            $response->redirect('/', 'Unable to Continue - Maximum number of blogs exceeded!', 'Error');
        }
        else {
            $newblogkey = $this->modelBlogs->createBlog($request->getString('fld_blogname'), $request->getString('fld_blogdesc'));

            if (!$newblogkey) {
                $response->redirect('/', 'Error creating blog please try again later', 'error');
            }

            if (!$this->modelContributors->addBlogContributor($currentUser['id'], 'a', $newblogkey)) {
                $response->redirect('/', 'Error adding to contributor please try again later', 'error');
            }

            $response->redirect('/blog/overview/' . $newblogkey, 'Blog created', 'Success');
        }
    }
}
