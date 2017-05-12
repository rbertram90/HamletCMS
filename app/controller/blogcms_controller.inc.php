<?php
namespace rbwebdesigns\blogcms;
use rbwebdesigns;

/**********************************************************************
  class MainController

  This is the controller which acts as the intermediatory between the
  model (database) and the view. Any requests to the model are sent from
  here rather than the view.

  The content generated here is then passed through the template. Any
  pages which are expected to return content will be passed the parameters
  $DATA and $params where:

    $DATA - an array of configuration variables to be passed through
    to the view. This will more than likely always be returned from the
    function unless it redirects elsewhere.

    structure - array (
        'page_title' => <string>,
        'page_description' => <string>,
        'includes_css' => <array:string>, - file paths relative to the root directory
        'includes_js' => <array:string>,
        'page_content' => <memo>,
        'page_menu_actions' => <memo>
    )
       
    $params - Miscellaneous inputs from the URL such as blog id again
    accessed in an array.
    
**********************************************************************/

class BlogcmsController extends GenericController
{
    // Class Variables
    private $modelBlogs;        // Blogs Model
    private $modelPosts;        // Posts Model
    private $modelContributors; // Contributors
    private $modelComments;        // Comments Model
    private $modelUsers;        // Users Model
    private $modelSecurity;        // Security Functions
    protected $view;

    // Constructor
    public function __construct($dbconn, $view) {
        // Initialise Models
        $this->modelBlogs = new ClsBlog($dbconn);
        $this->modelContributors = new ClsContributors($dbconn);
        $this->modelPosts = new ClsPost($dbconn);
        $this->modelComments = new ClsComment($dbconn);
        $this->modelUsers = $GLOBALS['modelUsers'];
        $this->modelSecurity = new rbwebdesigns\AppSecurity();
        $this->view = $view;
    }

    public function logout($params)
    {
        // Resume the session
        session_start();

        // Kill the session completely
        session_destroy();

        // Navigate to homepage
        header("location: /index.php");
    }
    
    /******************************************************************
        GET - General Pages
    ******************************************************************/
    
    /**
        View the blog cms main dashboard which shows all blogs that the user contributes to
    **/
    public function home($params)
    {
        // Get all blogs which current user contributes to
        $arrayBlogs = $this->modelContributors->getContributedBlogs($_SESSION['userid']);
        
        // Add in extra information
        foreach($arrayBlogs as $key => $blog)
        {
            // The users who can contribute to this blog
            $arrayBlogs[$key]['contributors'] = $this->modelContributors->getBlogContributors($blog['id']);
            
            // The lastest post for this blog
            $arrayBlogs[$key]['latestpost'] = $this->modelPosts->getLatestPost($blog['id']);
            
            // Format the lastest post date for this blog
            if(gettype($arrayBlogs[$key]['latestpost']) == 'array')
            {
                $formatteddate = rbwebdesigns\DateFormatter::formatFriendlyTime($arrayBlogs[$key]['latestpost']['timestamp']);
                $arrayBlogs[$key]['latestpost']['timestamp'] = 'Last posted: '.$formatteddate;
            }
            else
            {
                $lastposted = 'Currently Nothing Posted!';
            }
        }
        
        // Add to template
        $this->view->setVar('blogs', $arrayBlogs);
        
        // Get the current users favourite blogs
        $arrayFavoriteBlogs = $this->modelBlogs->getAllFavourites($_SESSION['userid']);
        $this->view->setVar('favoriteblogs', $arrayFavoriteBlogs);
        
        // Get recent posts from those blogs
        $this->view->setVar('recentposts', $this->modelPosts->getRecentPosts($arrayFavoriteBlogs, 7));
        
        $this->view->addScript('/js/showUserCard');

        // Set the page title
        $this->view->setPageTitle('My Blogs');
        
        // Output the view
        $this->view->render('index.tpl');
    }
    
    /**
        Explore Pages
    **/
    public function explore($params)
    {
        if(strlen($params[0]) == 0) $params[0] = 'blogsbyletter';
        
        switch(sanitize_string($params[0]))
        {
            case 'blogsbyletter':

            // Set Variables
            $this->view->setVar('counts', $this->modelBlogs->countBlogsByLetter());
            $this->view->setVar('alphabet', array('0','A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z'));

            if(array_key_exists(1, $params))
            {
                // Get Target Letter
                $currentletter = strlen($params[1]) == 1 ? sanitize_string($params[1]) : 'A';

                // Set Variables
                $this->view->setVar('letter', $currentletter);
                $this->view->setVar('blogs', $this->modelBlogs->getBlogsByLetter($currentletter));
            }

            // Page Title
            $this->view->setPageTitle('Explore Blogs - Browse Blogs By Letter');

            // Output the view
            $this->view->render('explore/browse.tpl');

            break;

            case 'popular':

            // Get most favourited blogs
            $this->view->setVar('topblogs', $this->modelBlogs->getTopFavourites());

            // Page Title
            $this->view->setPageTitle('Explore Blogs - Top Favourites');

            // Output the view
            $this->view->render('explore/popular.tpl');

            break;
                
            
            case 'category':
                
            $category = 'general';
            
            if(array_key_exists(1, $params)) $category = sanitize_string($params[1]);
            
            
            // Set categories from config
            $this->view->setVar('currentcategory', $category);
                
            // Set categories from config
            $this->view->setVar('categories', $GLOBALS['config']['blogcategories']);
            
            // Get most favourited blogs
            $this->view->setVar('blogs', $this->modelBlogs->getByCategory($category));

            // Page Title
            $this->view->setPageTitle('Explore Blogs by Category');
            
            // Output the view
            $this->view->render('explore/category.tpl');
            break;
        }
    }
    
    /**
        View the Documentation Page (Admin Link Only)
    **/
    public function viewDocs($params)
    {
        // Set page title
        $this->view->setPageTitle('Developer Documentation');
        
        // Include documentation CSS
        $this->view->addStylesheet('/css/docs');
        
        // Render View
        $this->view->render('documentation.tpl');
    }
    
    /**
        View the Welcome Page
    **/
    public function welcome($params)
    {
        // Page title
        $this->view->setPageTitle('Welcome to Blog CMS');
        
        // Render View
        $this->view->render('welcome.tpl');
    }
    
    /**
        View overview/ summary of a single blog
    **/
    public function blogOverview($params)
    {
        $blog_id = safeNumber($params[0]); // Try and get Blog ID
        
        // Check for Blog ID
        if(strlen($blog_id) == 0) return $this->home($params);
        
        // Check that user has permission to view this page
        if($this->modelContributors->isBlogContributor($blog_id, $_SESSION['userid']))
        {
            // Get info from blogs table
            $arrayBlog = $this->modelBlogs->getBlogById($blog_id);
            $this->view->setVar('blog', $arrayBlog);
            
            // Get latest 5 comments
            $latestcomments = $this->modelComments->getCommentsByBlog($blog_id, 5);
            
            // Get comment-ers usernames
            foreach($latestcomments as $key => $comment)
            {
                $user = $this->modelUsers->getUserById($comment['user_id']);
                $username = $comment['user_id'] == $_SESSION['userid'] ? "You" : $user['username'];
                $latestcomments[$key]['userid'] = $comment['user_id'];
                $latestcomments[$key]['name'] = $username;
            }
            $this->view->setVar('comments', $latestcomments);
            
            // Get latest 5 posts
            $this->view->setVar('posts', $this->modelPosts->getPostsByBlog($blog_id, 1, 5, 1, 1));
            
            // Get count statistics
            $this->view->setVar('counts', array(
                'posts' => $this->modelPosts->countPostsOnBlog($blog_id, true),
                'comments' => $this->modelComments->getCount(array('blog_id' => $blog_id)),
                'contributors' => $this->modelContributors->getCount(array('blog_id' => $blog_id)),
                'totalviews' => $this->modelPosts->countTotalPostViews($blog_id)
            ));
            
            // Set page title
            $this->view->setPageTitle('Dashboard - '.$arrayBlog['name']);
            
            // Output the view
            $this->view->render('overview.tpl');
        }
        else
        {
            include '403.php';
        }
    }
    
    
    /**
        manageComments
        @description view all comments that have been made on the blog and give the option to delete
        @param <array> split query string
        @notes Need to change this view to look more like the manage posts view with a seperate
        ajax call to get the comments themselves
    **/
    public function manageComments($params)
    {
        // Get the Blog ID
        $blog_id = sanitize_number($params[0]);
        
        // Check user has permissions to view comments
        if(!$this->modelContributors->isBlogContributor($blog_id, $_SESSION['userid'])) return $this->throwAccessDenied();
        
        // Check if we are deleting a comment
        if(array_key_exists(2, $params) && $params[1] == 'delete')
        {
            $commentID = safeNumber($params[2]);
            $this->deleteComment($commentID, $blog_id);
        }
        elseif(array_key_exists(2, $params) && $params[1] == 'approve') {
            $commentID = safeNumber($params[2]);
            $this->approveComment($commentID, $blog_id);
        }
        
        // View Current Comments
        $arrayComments = $this->modelComments->getCommentsByBlog($blog_id);
        
        // Get user data
        foreach($arrayComments as $key => $comment)
        {
            $arrayComments[$key]['user'] = $this->modelUsers->getUserById($comment['user_id']);
        }
        
        // Assign the variables in the view
        $this->view->setVar('comments', $arrayComments);
        $blog = $this->modelBlogs->getBlogById($blog_id);
        $this->view->setVar('blog', $blog);
        $this->view->setPageTitle('Manage Comments - '.$blog['name']);
        $this->view->addScript('/resources/js/paginate');
        
        // Output template
        $this->view->render('comments.tpl');
    }
    
    
    /**
        deleteComment
        @description remove a comment from a blog
        @param $commentID <int> Unique ID for the comment
        @param $blog_id <int> Unique ID for the blog
    **/
    protected function deleteComment($commentID, $blog_id)
    {
        // Delete from database
        $this->modelComments->delete($commentID);
        
        // Set the message to show on the next page
        setSystemMessage(ITEM_DELETED, 'Success');
        
        // Redirect back to comments page
        redirect('/comments/' . $blog_id);
    }
    
    /**
        approveComment
        @description set approved=1 for a comment
        @param $commentID <int> Unique ID for the comment
        @param $blog_id <int> Unique ID for the blog
    **/
    protected function approveComment($commentID, $blog_id)
    {
        // Update database
        $this->modelComments->approve($commentID);
        
        // Set the message to show on the next page
        setSystemMessage('Comment approved', 'Success');
        
        // Redirect back to comments page
        redirect('/comments/' . $blog_id);
    }
    
        
    /******************************************************************
        POST - Blogs
    ******************************************************************/
    
    /**
        Create a new blog
    **/
    public function action_createBlog()
    {
        // Check form key
        if(!isset($_SESSION['secure_form_key']) || $_POST['secure_form_key'] !== $_SESSION['secure_form_key']) die('Security Check Failed');
        unset($_SESSION['secure_form_key']);
        
        if(!IS_DEVELOPMENT && $this->modelBlogs->countBlogsByUser($_SESSION['userid']) > 4)
        {
            setSystemMessage('Unable to Continue - Maximum number of blogs exceeded!', 'Error');
        }
        else
        {
            $newblogkey = $this->modelBlogs->createBlog($_POST['fld_blogname'], $_POST['fld_blogdesc']);
            $this->modelContributors->addBlogContributor($_SESSION['userid'], 'a', $newblogkey);
            setSystemMessage(ITEM_CREATED, 'Success');
        }
        redirect('/');
    }
    
    /**
        View the new blog form
    **/
    public function createBlog($params)
    {        
        // Page Title
        $this->view->setPageTitle('Create New Blog');
        
        // Render the view
        $this->view->render('newblog.tpl');
    }
    
}
?>