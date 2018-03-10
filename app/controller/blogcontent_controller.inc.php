<?php
/*********************************************************************
  blog_content_controller
  this class is for front end actions on the blogs within blog_cms
  i.e. viewing posts, making comments etc.
*********************************************************************/

namespace rbwebdesigns\blogcms;
use Codeliner;

class BlogContentController {

    // Class Variables
    private $modelBlogs;       // Blogs Model
    private $modelPosts;       // Posts Model
    private $modelComments;    // Comments Model
    private $modelUsers;       // Users Model
    private $blog;             // Current viewing blog
    private $blogID;           // ID of current blog
    private $blogConfig;       // Config array of current blog
    private $blogPostCount;    // Number of posts on current blog
    private $userPermissionsLevel; // 0 = none, 1 = post only, 2 = full
    
    public $header_hideTitle = false;
    public $header_hideDescription = false;
    
    public function __construct($dbconn, $blog_key)
    {
        $currentUser = BlogCMS::session()->currentUser;

        // Create Models
        $this->modelBlogs        = new ClsBlog($dbconn);
        $this->modelContributors = new ClsContributors($dbconn);
        $this->modelPosts        = new ClsPost($dbconn);
        $this->modelComments     = new ClsComment($dbconn);
        $this->modelUsers        = $GLOBALS['modelUsers'];
                
        // Cached information for this blog
        $this->blog          = $this->modelBlogs->getBlogById($blog_key);
        $this->blogID        = $blog_key;
        $this->blogConfig    = null;
        $this->blogPostCount = null;
        
        if($this->modelContributors->isBlogContributor($blog_key, $currentUser, 'all')) {
            $this->userPermissionsLevel = 2;
        }
        elseif($this->userIsContributor()) {
            $this->userPermissionsLevel = 1;
        }
        else {
            $this->userPermissionsLevel = 0;
        }
    }
    
    
    /**
        function getBlogInfo()
        @return <array> All information from blog table for current blog
    **/
    public function getBlogInfo()
    {
        return $this->blog;
    }
    
    
    /**
        function getBlogID()
        @return <int> ID number for instance
    **/
    public function getBlogID()
    {
        return $this->blogID;
    }
    
    
    /**
        function getPostCount()
        @return <int> number of posts on blog
    **/
    public function getPostCount()
    {
        // Check Variable Cache
        if($this->blogPostCount === null) {
            $this->blogPostCount = $this->modelPosts->countPostsOnBlog($this->blogID);
        }
        return $this->blogPostCount;
    }
    
    
    /**
        Check if the current user is a contributor of this blog
        @return <bool> true if user is a contributor, false otherwise
    **/
    public function userIsContributor()
    {
        $currentUser = BlogCMS::session()->currentUser;
        if(!$currentUser) return false;
        return $this->modelContributors->isBlogContributor($this->blogID, $currentUser);
    }

    
    /**
        Check if this blog is currently listed in the users favourites
        @return <bool> true if blog is in current users favourites, false otherwise
    **/
    public function blogIsFavourite()
    {
        $currentUser = BlogCMS::session()->currentUser;
        if(!isset($currentUser)) return false;
        return $this->modelBlogs->isFavourite($currentUser, $this->blogID);
    }
    
    
    /**
        View blog homepage
        @return <array> data for template
    **/
    public function viewHome($DATA, $queryParams)
    {
        // Include the view posts functions
        include SERVER_ROOT.'/app/view/view_posts.php';
        
        // Check blog post count
        $numPosts = $this->getPostCount();
        
        // Set page title
        $DATA['page_title'] = $this->blog['name'];
        
        if($numPosts === 0) {
            // No Posts
            echo showInfo("There are no posts to show on this blog right now");
            return $DATA;
        }
        
        $pageNum = (isset($_GET['s']) && $_GET['s'] > 0) ? safeNumber($_GET['s']) : 1;
        // $pageNum = Request::GetNumberVariable('s');
        $blogConfig = $this->getBlogConfig($this->blogID);
        $postsperpage = getNumPostsToView($blogConfig);
        
        // Fetch Posts from the Database
        $arrayPosts = $this->modelPosts->getPostsByBlog($this->blogID, $pageNum, $postsperpage);
        
        // View Posts
        viewMultiplePosts($arrayPosts, $this->blogID, $blogConfig, $numPosts, $pageNum);
        return $DATA;
    }


    /**
        View the search page which allows you to free text search blog posts for this blog
    **/
    public function search($DATA, $queryParams) {
        
        // Perform Search
        if(isset($_GET['q'])) {
            $search_string = sanitize_string($_GET['q']);
            $search_results = $this->modelPosts->search($this->blogID, $search_string);
        }
        else $search_string = null;
        
        // Output results
        require_once SERVER_ROOT.'/app/view/search_blog_posts.php';
        
        $DATA['page_title'] = "Search";
        return $DATA;
    }
    
    
    /**
        Output the top menu of pages which the user has selected
        @return nothing
    **/
    public function generatePagelist() {
        echo $this->blog['pagelist'];
    }


    /**
        Each link is a blog post which has been marked as a 'page' through the settings menus
        @return <string> html for a top navigation bar
    **/
    public function generateNavigation() {
        if(!array_key_exists('pagelist', $this->blog) || strlen($this->blog['pagelist']) == 0) {
            return '';
        }
        $navigation = '';
        $pagelist = explode(',', $this->blog['pagelist']);

        foreach($pagelist as $postid) {
            if(is_numeric($postid))
            {
                $arrayPosts = $this->modelPosts->get('*', array('id' => $postid));
                $arrayPost = $arrayPosts[0];
                $navigation.= '<a href="/blogs/'.$arrayPost['blog_id'].'/posts/'.$arrayPost['link'].'">'.$arrayPost['title'].'</a>';
            }
            elseif(substr($postid, 0, 2) == 't:')
            {
                $tag = substr($postid, 2);
                $navigation.= '<a href="/blogs/'.$this->blog['id'].'/tags/'.$tag.'">'.$tag.'</a>';
            }
        }
        return $navigation;
    }
    

    /**
        Generate the HTML for the widgets on the blog
        @return <string> Page HTML
    **/
    public function generateWidgets() {
        
        // replace quotes - caused by sanitize_string when storing in database - should we be doing this?
        $blogJSON = str_replace("&#34;", '"', $this->blog['widgetJSON']);
        
        // Convert config to array
        $arrayWidgets = json_decode($blogJSON, true);
        
        // Default the JSON if none found - again this should be saved elsewhere...
        // if(strlen($blogJSON) == 0) $blogJSON = '{"profile":{"order":1,"show":1},"postlist":{"order":2,"show":1},"taglist":{"order":3,"show":1},"subscribers":{"order":4,"show":1},"comments":{"order":5,"show":1}}';
        
        // View
        require SERVER_ROOT.'/app/view/widgets/widgets.php';
        return generateWidgets($arrayWidgets, $this->modelPosts, $this->modelBlogs, $this->modelComments, $this->blog, $this->modelUsers);
    }
    
    public function generateWidgets2() {
        $widgetConfigPath = SERVER_PATH_BLOGS . '/' . $this->blog['id'] . '/widgets.json';
        
        if(!file_exists($widgetConfigPath)) return '';
        
        $widgetConfig = rbwebdesigns\JSONhelper::jsonToArray($widgetConfigPath);
        
        
    }
    

    /**
        Generate the HTML to be shown in the footer
        @return <string> html
    **/
    public function generateFooter() {
                
        // Get the JSON blog config
        $blogConfig = $this->getBlogConfig($this->blogID);
        
        // Check that the footer key exists
        if(strtolower(gettype($blogConfig)) !== 'array' || !array_key_exists('footer', $blogConfig)) return '';
        
        // Get data from config
        $configReader = new Codeliner\ArrayReader\ArrayReader($blogConfig);
        $numcols = $configReader->integerValue('footer.numcols', 1);                     // Number of columns
        $contentColumn1 = $configReader->stringValue('footer.content_col1', false);      // Content for column 1
        $contentColumn2 = $configReader->stringValue('footer.content_col2', false);      // Content for column 2
        $backgroundImage = $configReader->stringValue('footer.background_image', false); // Background Image
        
        // Generate Content HTML
        if($numcols == 1 && $contentColumn1) {
            // Single Column
            $footerContent = $contentColumn1;
            
        } elseif($numcols == 2 && $contentColumn1 && $contentColumn2) {
            // Two Column Layout
            $footerContent = '<div class="cols2_1">'.$contentColumn1.'</div><div class="col_sep"></div>';
            $footerContent.= '<div class="cols2_2">'.$contentColumn2.'</div>';
            
        } else {
            // No content
            $footerContent = '';
        }
        
        // Generate Background CSS
        if($backgroundImage && strlen($blogConfig['footer']['background_image']) > 0) {
            
            // Background position
            $h = $configReader->stringValue('footer.bg_image_post_horizontal', false);
            $v = $configReader->stringValue('footer.bg_image_post_vertical', false);
            
            if($h != 'r' && $v == 'n') $br = 'background-repeat:repeat-x;';
            elseif($h == 'n' && $v != 'r') $br = 'background-repeat:repeat-y;';
            elseif($h == 'r' && $v == 'r') $br = 'background-repeat:repeat;'; // repeat both
            else $br = '';
            
            if($h == 's' && $v == 's') $br.= 'background-size:100% 100%;';
            elseif($h == 's') $br.= 'background-size:100% auto;'; // stretch
            elseif($v == 's') $br.= 'background-size:auto 100%;';
            
            $footerContent.= '<style type="text/css">
                .footer {
                    background-image:url("'.$backgroundImage.'");
                    '.$br.'
                }
            </style>';
        }
        
        return $footerContent;
    }
    
    
    /**
        Generate the CSS for the header background
        @return <string> html style tag for header
    **/
    public function generateHeaderBackground() {
        
        // Get the JSON blog config
        $blogConfig = $this->getBlogConfig($this->blogID);
        
        // Check header config exists
        if(getType($blogConfig) !== 'array' || !array_key_exists('header', $blogConfig)) return '';
        
        $headerContent = '';
        $configReader = new Codeliner\ArrayReader\ArrayReader($blogConfig);
        $backgroundImage = $configReader->stringValue('header.background_image', false); // Background Image
        
        // Generate background CSS
        if($backgroundImage && strlen($blogConfig['header']['background_image']) > 0) {
            
            // Background-position
            $h = $configReader->stringValue('header.bg_image_post_horizontal', false);
            $v = $configReader->stringValue('header.bg_image_post_vertical', false);
            $ha = $configReader->stringValue('header.bg_image_align_horizontal', false);
            
            if($h == 'r' && $v == 'r') $br = "background-repeat:repeat;"; // repeat both
            elseif($h == 'r') $br = "background-repeat:repeat-x;";
            elseif($v == 'r') $br = "background-repeat:repeat-y;";
            else $br = 'background-repeat:no-repeat;';
            
            if($h == 's' && $v == 's') $br.= "background-size:100% 100%;";
            elseif($h == 's') $br.= "background-size:100% auto;"; // stretch
            elseif($v == 's') $br.= "background-size:auto 100%;";
            
            if($h == 'n' && $ha == 'r') $br.= " background-position: right center;";
            elseif($h == 'n' && $ha == 'c') $br.= " background-position: center center;";
            elseif($h == 'n' && $ha == 'l') $br.= " background-position: left center;";
            
            $headerContent = '<style type="text/css">
                .header {
                    background-image:url("'.$backgroundImage.'");
                    '.$br.'
                }
            </style>';
        }
        
        // Set flags for hiding title and/or description
        // We're doing it here becuase this is where we are looking at the header config
        // However we are not modifying the DATA variable here
        $ht = $configReader->stringValue('header.hide_title', false);
        $hd = $configReader->stringValue('header.hide_description', false);
                
        if($ht == 'on') $this->header_hideTitle = true;
        if($hd == 'on') $this->header_hideDescription = true;
        
        return $headerContent;
    }
    
    
    /**
        Show all posts matching tag
    **/
    public function viewPostsByTag($DATA, $queryParams) {

        // Include view functions
        include SERVER_ROOT.'/app/view/view_posts.php';

        // Page Heading
        echo "<h1>Posts tagged with '".$queryParams[0]."'</h1>";

        // Fetch Posts from DB
        $postlist = $this->modelPosts->getBlogPostsByTag($this->blogID, $queryParams[0]);

        // Current Page
        $pageNum = isset($_GET['s']) ? safeNumber($_GET['s']) : 1;

        // Get post count
        $numPosts = count($postlist);

        // Generate Page HTML
        if($numPosts > 0) viewMultiplePosts($postlist, $this->blogID, $this->getBlogConfig($this->blogID), $numPosts, $pageNum);
        else echo showInfo("There are no posts tagged with <i>'".$queryParams[0]."'</i> on this blog");

        // Set Page Title
        $DATA['page_title'] = 'Posts tagged with '.$queryParams[0].' - '.$this->blog['name'];

        return $DATA;
    }
    


    /**
        Get the blog config file 'config.json' as an array
    **/
    private function getBlogConfig($blogid) {
        // Check variable cache
        if($this->blogConfig === null) {
            $settings = file_get_contents(SERVER_PATH_BLOGS.'/'.$blogid.'/config.json');
            $this->blogConfig = json_decode($settings, true);
        }
        return $this->blogConfig;
    }
    


    /**
        getFontFamilyFromName($fontName as String)
        @return <string> CSS String for font-family rule
    **/
    public function getFontFamilyFromName($fontName) {
        $fontarray = array(
            "ARIAL" => "Arial, Helvetica, sans-serif",
            "CALIBRI" => "Calibri, sans-serif",
            "COMICSANS" => "'Comic Sans MS', cursive",
            "COURIER" => "'Courier New', monospace",
            "IMPACT" => "Impact, Charcoal, sans-serif",
            "LUCIDA" => "'Lucida Console', Monaco, monospace",
            "TAHOMA" => 'Tahoma, Geneva, sans-serif',
            "TREBUCHET" => "'Trebuchet MS', sans-serif"
        );

        if(array_key_exists($fontName, $fontarray)) return $fontarray[$fontName];
        else return "Arial, Helvetica, sans-serif";
    }
    
    
    public function getTemplateConfig()
    {
        $lsSettings = file_get_contents(SERVER_PATH_BLOGS.'/'.$this->blog['id'].'/template_config.json');
        return json_decode($lsSettings, true);
    }
    
    /**
        Generates the CSS for a blog specified in the JSON file 'template_config.json'
        which should exist under the $pblogid folder
    **/
    public function getBlogCustomCSS($pblogid) {
    
        $lobjSettings = $this->getTemplateConfig();
        $css = "";
        
        if(count($lobjSettings) == 0) return;
        
        foreach($lobjSettings as $key => $lobjClass):
            if(strtolower($key) == 'layout') continue;
        
            // 0 should always be class name
             $css.= '.'.$lobjClass[0].' {';
            
            foreach($lobjClass as $rule):
                // Check it is an array
                if(gettype($rule) !== "array") continue;
                if($rule['current'] === $rule['default']) continue;
                
                switch($rule['type']):
                    case "bgcolor":
                        $css.= "background-color:#".$rule['current'].';';
                        break;
                    case "color":
                        $css.= 'color:#'.$rule['current'].';';
                        break;
                    case "font":
                        $css.= 'font-family:'.$this->getFontFamilyFromName($rule['current']).';';
                        break;
                    case "textsize":
                        $css.= 'font-size:'.$rule['current'].'px;';
                        break;
                endswitch;
            endforeach;
            $css.= '}';
        endforeach;
        
        return $css;
    }
    
    
    /**
        addView($postid as int)
        Record that user has viewed the post
    **/
    public function addView($postid) {
        
        $arrayVisitors = $this->modelPosts->getViewsByPost($postid);
        $userip = $_SERVER['REMOTE_ADDR'];
        $countUpdated = false;
                
        foreach($arrayVisitors as $visitor) {
            if($userip == $visitor['userip']) {
                // already visited this page
                // $userviews = $visitor['viewcount'] + 1;
    // echo "pre".$userviews;
                $this->modelPosts->incrementUserView($postid, $userip, $visitor['userviews']);
                $countUpdated = true;
                break;
            }
        }
        
        if(!$countUpdated) {
            // New Visitor
            $this->modelPosts->recordUserView($postid, $userip);
        }
    }
    
    
    /**
        View Individual Post
    **/
    public function viewPost($DATA, $queryParams) {

        // Include the view post functions
        include SERVER_ROOT.'/app/view/view_posts.php';
        
        // Get post info from DB
        $arrayPost = $this->modelPosts->getPostByURL($queryParams[0], $DATA['blog_key']);
        
        // Check conditions in which the user is not allowed to view the post
        if($arrayPost) {
            if($arrayPost['draft'] == 1 && !$this->userIsContributor()) {
                $arrayPost = false; // user not entitled to view post
            }
            elseif(strtotime($arrayPost['timestamp']) > time() && !$this->userIsContributor()) {
                $arrayPost = false; // post has not yet been released in the wild!
            }
        }

        // Check post is still valid to view by user
        if($arrayPost) {
            
            // Get next and back posts
            $nextPost = $this->modelPosts->getNextPost($this->blogID, $arrayPost['timestamp']);
            $prevPost = $this->modelPosts->getPreviousPost($this->blogID, $arrayPost['timestamp']);
            
            // Output the HTML
            viewSinglePost($arrayPost, $this->getBlogConfig($this->blogID), $prevPost, $nextPost);
            
            // Generate Comment Section
            if($arrayPost['allowcomments'] == 1) {
                // View comment form
                $lobjBlogID = $this->modelPosts->getPostByURL($queryParams[0], $this->blogID);
                $arrayComments = $this->modelComments->getCommentsByPost($lobjBlogID['id'], false);
                viewComments($arrayComments);
                commentForm($this->blog, $arrayPost);
            }
            
            // Count the view
            $this->addView($arrayPost['id']);

        }
        else echo showInfo("Unable to find post");

        $DATA['page_title'] = $arrayPost['title'];
        return $DATA;
    }
    
    /************* Comments ******************/
    
    public function addComment($DATA, $queryParams)
    {
        // todo: Check that the user hasn't submitted more than 5 comments in last 30 seconds?
        // Or if the last X comments were from the same user?
        // to prevent comment spamming
        
        // Check that the comment was accutally submitted?
        // Stops people just browsing to the /addcomment URL
        $postID = sanitize_number($queryParams[0]);
        
        // Validate comment
        $formValid = true;

        $currentUser = BlogCMS::session()->currentUser;
        
        if(!isset($_POST['fld_submitcomment'])) $formValid = false;
        
        if(!isset($_POST['fld_comment']) || strlen($_POST['fld_comment']) == 0)
        {
            $formValid = false;
            setSystemMessage("Please enter a comment", "Error");
        }
        
        if($formValid)
        {
            // Get the post from DB
            $post = $this->modelPosts->getPostByID($postID, $DATA['blog_key']);
            
            // Check that post allows reader comments
            if($post['allowcomments'] == 0)
            {
                setSystemMessage("Failed to add comment", "Error");
            }
            else
            {
                // Sanitize Comment
                $content = sanitize_string($_POST['fld_comment']);
                
                // Submit to DB
                $this->modelComments->addComment($content, $post['id'], $DATA['blog_key'], $currentUser);
                
                // Show Success
                setSystemMessage("Comment submitted - awaiting approval", "Success");
            }
        }
        
        redirect("/blogs/{$DATA['blog_key']}/posts/{$post['link']}");
    }
}
?>