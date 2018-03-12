<?php
namespace rbwebdesigns\blogcms;
use rbwebdesigns\core\AppSecurity;
use rbwebdesigns\core\Sanitize;
use rbwebdesigns\core\DateFormatter;

/**
 * class MainController
 * 
 * This is the controller which acts as the intermediatory between the
 * model (database) and the view. Any requests to the model are sent from
 * here rather than the view.
 *
 * The content generated here is then passed through the template. Any
 * pages which are expected to return content will be passed the parameters
 * $DATA and $params where:
 *
 *  $DATA - an array of configuration variables to be passed through
 *  to the view. This will more than likely always be returned from the
 *  function unless it redirects elsewhere.

 *  structure - array (
 *      'page_title' => <string>,
 *      'page_description' => <string>,
 *      'includes_css' => <array:string>, - file paths relative to the root directory
 *      'includes_js' => <array:string>,
 *      'page_content' => <memo>,
 *      'page_menu_actions' => <memo>
 *  )
 *     
 *  $params - Miscellaneous inputs from the URL such as blog id again
 *  accessed in an array.
 * 
 * @author R Bertram <ricky@rbwebdesigns.co.uk>
 */
class BlogController extends GenericController
{
    // Class Variables
    private $modelBlogs;        // Blogs Model
    private $modelPosts;        // Posts Model
    private $modelContributors; // Contributors
    private $modelComments;     // Comments Model
    private $modelUsers;        // Users Model
    private $modelSecurity;     // Security Functions
    protected $view;

    // Constructor
    public function __construct() {
        // Initialise Models
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\model\Blogs');
        $this->modelContributors = BlogCMS::model('\rbwebdesigns\blogcms\model\Contributors');
        $this->modelPosts = BlogCMS::model('\rbwebdesigns\blogcms\model\Posts');
        $this->modelComments = BlogCMS::model('\rbwebdesigns\blogcms\model\Comments');
        $this->modelUsers = BlogCMS::model('\rbwebdesigns\blogcms\model\AccountFactory');
        // $this->modelSecurity = new AppSecurity();
        // $this->view = $view;
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
    
    /******************************************************************
        GET - General Pages
    ******************************************************************/
    
    /**
     * View the blog cms main dashboard which shows all blogs that the user contributes to
     */
    public function home(&$request, &$response)
    {
        $user = BlogCMS::session()->currentUser; // currently coming out as number - suspect this will change...

        // Get all blogs which current user contributes to
        $arrayBlogs = $this->modelContributors->getContributedBlogs($user['id']);
        
        // Add in extra information
        foreach($arrayBlogs as $key => $blog) {
            // The users who can contribute to this blog
            $arrayBlogs[$key]['contributors'] = $this->modelContributors->getBlogContributors($blog['id']);
            
            // The lastest post for this blog
            $arrayBlogs[$key]['latestpost'] = $this->modelPosts->getLatestPost($blog['id']);
            
            // Format the lastest post date for this blog
            if(gettype($arrayBlogs[$key]['latestpost']) == 'array') {
                $formatteddate = DateFormatter::formatFriendlyTime($arrayBlogs[$key]['latestpost']['timestamp']);
                $arrayBlogs[$key]['latestpost']['timestamp'] = 'Last posted: '.$formatteddate;
            }
            else {
                $lastposted = 'Currently Nothing Posted!';
            }
        }
        
        // Add to template
        $response->setVar('blogs', $arrayBlogs);
        
        // Get the current users favourite blogs
        $arrayFavoriteBlogs = $this->modelBlogs->getAllFavourites($user['id']);
        $response->setVar('favoriteblogs', $arrayFavoriteBlogs);
        $response->setVar('recentposts', $this->modelPosts->getRecentPosts($arrayFavoriteBlogs, 7));
        
        $response->addScript('/js/showUserCard');
        $response->setTitle('My Blogs');
        $response->write('index.tpl');
    }
    
    /**
        Explore Pages
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
     *  View overview/ summary of a single blog
     */
    public function overview(&$request, &$response)
    {
        $blogID = $request->getUrlParameter(1);
        $currentUser = BlogCMS::session()->currentUser;

        // Validation
        if(strlen($blogID) == 0) {
            redirect('/');
        }
        elseif(!$this->modelContributors->isBlogContributor($blogID, $currentUser['id'])) {
            redirect('/', 'You do not contribute to this blog', 'error');
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
        
        // Set page title
        $response->setTitle('Dashboard - '.$blog['name']);
        
        // Output the view
        $response->write('overview.tpl');
    }
            
    /******************************************************************
        POST - Blogs
    ******************************************************************/
    
    /**
     * Create a new blog
     */
    public function action_createBlog()
    {
        // Check form key
        // if(!isset($_SESSION['secure_form_key']) || $_POST['secure_form_key'] !== $_SESSION['secure_form_key']) die('Security Check Failed');
        // unset($_SESSION['secure_form_key']);
        
        $currentUser = BlogCMS::session()->currentUser;

        if(!IS_DEVELOPMENT && $this->modelBlogs->countBlogsByUser($currentUser) > 4) {
            setSystemMessage('Unable to Continue - Maximum number of blogs exceeded!', 'Error');
        }
        else {
            $newblogkey = $this->modelBlogs->createBlog($_POST['fld_blogname'], $_POST['fld_blogdesc']);
            $this->modelContributors->addBlogContributor($currentUser, 'a', $newblogkey);
            setSystemMessage(ITEM_CREATED, 'Success');
        }
        redirect('/');
    }
    
    /**
     * View the new blog form
     */
    public function createBlog($params)
    {        
        // Page Title
        $this->view->setPageTitle('Create New Blog');
        
        // Render the view
        $this->view->render('newblog.tpl');
    }
    
}
?>