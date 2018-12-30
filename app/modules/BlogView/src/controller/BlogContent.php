<?php
namespace rbwebdesigns\blogcms\BlogView\controller;

use Codeliner;
use rbwebdesigns\core\Sanitize;
use rbwebdesigns\core\Pagination;
use rbwebdesigns\core\JSONhelper;
use rbwebdesigns\blogcms\BlogCMS;
use rbwebdesigns\blogcms\BlogCMSResponse;

/**
 * blog_content_controller
 * this class is for front end actions on the blogs within blog_cms
 * i.e. viewing posts, making comments etc.
 */
class BlogContent
{
    protected $modelBlogs;       // Blogs Model
    protected $modelPosts;       // Posts Model
    protected $modelComments;    // Comments Model
    protected $modelUsers;       // Users Model
    protected $blog;             // Current viewing blog
    protected $blogID;           // ID of current blog
    protected $blogConfig;       // Config array of current blog
    protected $blogPostCount;    // Number of posts on current blog
    protected $userPermissionsLevel; // 0 = none, 1 = post only, 2 = full
    protected $request;
    protected $response;
    
    public $header_hideTitle = false;
    public $header_hideDescription = false;
    
    public function __construct($blog_key)
    {
        $currentUser = BlogCMS::session()->currentUser;

        // Create Models
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\Blog\model\Blogs');
        $this->modelContributors = BlogCMS::model('\rbwebdesigns\blogcms\Contributors\model\Contributors');
        $this->modelPosts = BlogCMS::model('\rbwebdesigns\blogcms\BlogPosts\model\Posts');
        $this->modelComments = BlogCMS::model('\rbwebdesigns\blogcms\PostComments\model\Comments');
        $this->modelUsers = BlogCMS::model('\rbwebdesigns\blogcms\UserAccounts\model\UserAccounts');
        
        // Cached information for this blog
        $this->blog          = $this->modelBlogs->getBlogById($blog_key);
        $this->blogID        = $blog_key;
        $this->blogConfig    = null;
        $this->blogPostCount = null;

        if (CUSTOM_DOMAIN) {
            $this->pathPrefix = '';
            $this->fileDir = '';
            $this->fileDir = '';
        }
        else {
            $this->pathPrefix = "/blogs/{$this->blogID}";
            $this->fileDir = "/blogdata/{$this->blogID}";
        }

        // todo: Update this...
        if ($this->modelContributors->isBlogContributor($currentUser, $blog_key)) {
            $this->userPermissionsLevel = 2;
        }
        elseif (BlogCMS::$userGroup) {
            $this->userPermissionsLevel = 1;
        }
        else {
            $this->userPermissionsLevel = 0;
        }

        $this->request = BlogCMS::request();
        $this->response = BlogCMS::response();
    }
    
    /**
     * function getBlogInfo()
     * @return array All information from blog table for current blog
     */
    public function getBlogInfo()
    {
        return $this->blog;
    }
    
    /**
     *  function getBlogID()
     *  @return int ID number for instance
     */
    public function getBlogID()
    {
        return $this->blogID;
    }
    
    /**
     * function getPostCount()
     * @return int number of posts on blog
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
     * Check if this blog is currently listed in the users favourites
     * @return bool true if blog is in current users favourites, false otherwise
     */
    public function blogIsFavourite()
    {
        $currentUser = BlogCMS::session()->currentUser;
        if(!isset($currentUser)) return false;
        return false; // $this->modelBlogs->isFavourite($currentUser, $this->blogID);
    }
    
    /**
     * Generate HTML for the 'teaser' view for a post
     * 
     * @param array $post
     *   Post database record
     * @param array $config
     *   Post view settings
     */
    public function generatePostTeaser($post, $config)
    {
        $teaserResponse = new BlogCMSResponse();
        $teaserResponse->enableSecureMode();

        // Config defaults
        $showTags = 1;
        $shownumcomments = 1;
        $showsocialicons = 1;
        $summarylength = 150;
        $postsperpage = 5;

        if (isset($config['showtags']))
            $teaserResponse->setVar('showtags', $config['showtags']);
        if (isset($config['shownumcomments']))
            $teaserResponse->setVar('shownumcomments', $config['shownumcomments']);
        if (isset($config['showsocialicons']))
            $teaserResponse->setVar('showsocialicons', $config['showsocialicons']);
        if (isset($config['postsperpage']))
            $teaserResponse->setVar('postsperpage', $config['postsperpage']);

        // Copy accross sub-set of variables from main template
        $globalResponse = BlogCMS::response();
        $teaserResponse->setVar('blog_root_url', $globalResponse->getVar('blog_root_url'));
        $teaserResponse->setVar('blog_file_dir', $globalResponse->getVar('blog_file_dir'));
        $teaserResponse->setVar('user_is_contributor', $globalResponse->getVar('userIsContributor'));

        BlogCMS::runHook('runTemplate', ['template' => 'postTeaser', 'post' => &$post, 'config' => &$config]);

        // Check if blog template is overriding the teaser
        // @todo - find this once and store in config?!
        if (file_exists(SERVER_PATH_BLOGS .'/'. $this->blogID .'/templates/teaser.tpl')) {
            $templatePath = 'file:'. SERVER_PATH_BLOGS .'/'. $this->blogID .'/templates/teaser.tpl';
            $source = '';
        }
        else {
            // Use system default
            $templatePath = 'posts/teaser.tpl';
            $source = 'BlogView';
        }

        $teaserResponse->setVar('config', $config);
        $teaserResponse->setVar('post', $post);

        return $teaserResponse->write($templatePath, $source, false);
    }


    /**
     * Generate the output for a single post view
     * 
     * @param \rbwebdesigns\blogcms\BlogPosts\Post $post
     * @param array $config
     */
    public function generateSinglePost($post, $config)
    {
        $teaserResponse = new BlogCMSResponse();

        // Copy accross sub-set of variables from main template
        $globalResponse = BlogCMS::response();
        $teaserResponse->setVar('blog_root_url', $globalResponse->getVar('blog_root_url'));
        $teaserResponse->setVar('blog_file_dir', $globalResponse->getVar('blog_file_dir'));
        $teaserResponse->setVar('userIsContributor', $globalResponse->getVar('user_is_contributor'));
        $teaserResponse->setVar('userAuthenticated', $globalResponse->getVar('user_is_logged_in'));

        $post->after = [];

        BlogCMS::runHook('runTemplate', ['template' => 'singlePost', 'post' => &$post, 'config' => &$config]);

        // Check if blog template is overriding the teaser
        // @todo - find this once and store in config?!
        if (file_exists(SERVER_PATH_BLOGS .'/'. $this->blogID .'/templates/singlepost.tpl')) {
            $templatePath = 'file:'. SERVER_PATH_BLOGS .'/'. $this->blogID .'/templates/singlepost.tpl';
            $source = '';
        }
        else {
            // Use system default
            $templatePath = 'posts/singlepost.tpl';
            $source = 'BlogView';
        }

        $teaserResponse->setVar('config', $config);
        $teaserResponse->setVar('post', $post);

        // Output the content direct - this is inconsistant with the teaser version
        $teaserResponse->write($templatePath, $source, true);
    }

    /**
     * Generate the HTML for a post (either full or teaser)
     * 
     * @param \rbwebdesigns\blogcms\BlogPosts\Post $post
     * @param array $config
     * @param string $mode
     *   full / teaser modes accepted
     */
    public function generatePostTemplate($post, $config, $mode = 'full')
    {
        if (strlen($post->tags) > 0) {
            $post->tags = explode(',', $post->tags);
        }
        else {
            $post->tags = [];
        }

        $post->headerDate = $this->formatDateFromSettings($post->timestamp, $config, 'title');
        $post->footerDate = $this->formatDateFromSettings($post->timestamp, $config, 'footer');

        switch ($mode) {
            case 'full':
                return $this->generateSinglePost($post, $config);
                break;
            case 'teaser':
                return $this->generatePostTeaser($post, $config);
                break;
        }
    }

    /**
     *  View blog homepage
     *  @return <array> data for template
     */
    public function viewHome(&$request, &$response)
    {
        $pageNum = $request->getInt('s', 1);

        $blogConfig = $this->blog->config();
        $postConfig = null;
        $showTags = 1;
        $shownumcomments = 1;
        $showsocialicons = 1;
        $summarylength = 150;
        $postsperpage = 5;

        if (isset($blogConfig['posts'])) {
            $postConfig = $blogConfig['posts'];
            if (isset($postConfig['postsummarylength'])) $summarylength = $postConfig['postsummarylength'];
        }

        $postlist = $this->modelPosts->getPostsByBlog($this->blogID, $pageNum, $postsperpage);
        $output = "";

        $isContributor = BlogCMS::$userGroup !== false;
        $response->setVar('userIsContributor', $isContributor);

        foreach ($postlist as $post) {
            $output.= $this->generatePostTemplate($post, $postConfig, 'teaser');
        }
        
        // Pagination
        $response->setVar('postsperpage', $postsperpage);
        $response->setVar('totalnumposts', $this->modelPosts->count(['blog_id' => $this->blogID]));

        $response->setTitle($this->blog->name);
        $response->setVar('posts', $output);
        $response->setVar('paginator', new Pagination());
        $response->setVar('blog', $this->blog);
        $response->write('posts/postshome.tpl', 'BlogView');
    }

    /**
     * View the search page which allows you to free text search blog posts for this blog
     */
    public function search($DATA, $queryParams)
    {
        // Perform Search
        if(isset($_GET['q'])) {
            $search_string = Sanitize::string($_GET['q']);
            $search_results = $this->modelPosts->search($this->blogID, $search_string);
        }
        else $search_string = null;
        
        // Output results
        require_once SERVER_ROOT.'/app/view/search_blog_posts.php';
        
        $DATA['page_title'] = "Search";
        return $DATA;
    }
    
    /**
     * Output the top menu of pages which the user has selected
     */
    public function generatePagelist()
    {
        echo $this->blog->pagelist;
    }

    /**
     * Each link is a blog post which has been marked as a 'page' through the settings menus
     * @return string html for a top navigation bar
    **/
    public function generateNavigation()
    {
        if (strlen($this->blog->pagelist) == 0) {
            return '';
        }
        $navigation = '';
        $pagelist = explode(',', $this->blog->pagelist);

        foreach ($pagelist as $postid) {
            if (is_numeric($postid)) {
                $arrayPosts = $this->modelPosts->get('*', ['id' => $postid]);
                $post = $arrayPosts[0];
                $navigation.= '<a href="'. $this->pathPrefix .'/posts/'. $post->link .'" class="item">'. $post->title .'</a>';
            }
            elseif (substr($postid, 0, 2) == 't:') {
                $tag = substr($postid, 2);
                $navigation.= '<a href="'. $this->pathPrefix .'/tags/'. $tag .'" class="item">'. ucfirst($tag) .'</a>';
            }
        }
        return $navigation;
    }
    
    /**
     * Generate the HTML to be shown in the footer
     * @return string html
     */
    public function generateFooter()
    {
        // Get the JSON blog config
        $blogConfig = $this->blog->config();
        
        // Check that the footer key exists
        if(strtolower(gettype($blogConfig)) !== 'array' || !array_key_exists('footer', $blogConfig)) return '';
        
        // Get data from config
        $configReader = new Codeliner\ArrayReader\ArrayReader($blogConfig);
        $numcols = $configReader->integerValue('footer.numcols', 1);                     // Number of columns
        $contentColumn1 = $configReader->stringValue('footer.content_col1', false);      // Content for column 1
        $contentColumn2 = $configReader->stringValue('footer.content_col2', false);      // Content for column 2
        $backgroundImage = $configReader->stringValue('footer.background_image', false); // Background Image
        
        // Generate Content HTML
        if ($numcols == 1 && $contentColumn1) {
            // Single Column
            $footerContent = $contentColumn1;
            
        } elseif ($numcols == 2 && $contentColumn1 && $contentColumn2) {
            // Two Column Layout
            $footerContent = '<div class="cols2_1">'.$contentColumn1.'</div><div class="col_sep"></div>';
            $footerContent.= '<div class="cols2_2">'.$contentColumn2.'</div>';
        } else {
            // No content
            $footerContent = '';
        }
        
        // Generate Background CSS
        if ($backgroundImage && strlen($blogConfig['footer']['background_image']) > 0)
        {
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
                .page-footer {
                    background-image:url("'.$backgroundImage.'");
                    '.$br.'
                }
            </style>';
        }
        
        return $footerContent;
    }
    
    /**
     * Generate the CSS for the header background
     * @return string html style tag for header
     */
    public function generateHeaderBackground()
    {
        // Get the JSON blog config
        $blogConfig = $this->blog->config();
        
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
                .page-header {
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
     * Show all posts matching tag
     */
    public function viewPostsByTag(&$request, &$response)
    {
        $tag = $request->getUrlParameter(2);
        $pageNum = $request->getInt('s', 1);
        $postlist = $this->modelPosts->getBlogPostsByTag($this->blogID, $tag);

        $isContributor = false;
        if ($currentUser = BlogCMS::session()->currentUser) {
            $isContributor = $this->modelContributors->isBlogContributor($currentUser['id'], $this->blogID);
        }

        $blogConfig = $this->blog->config();
        $postConfig = null;
        $showTags = 1;
        $shownumcomments = 1;
        $showsocialicons = 1;
        $summarylength = 150;
        $postsperpage = 5;

        if (isset($blogConfig['posts'])) {
            $postConfig = $blogConfig['posts'];
            if (isset($postConfig['postsperpage'])) $postsperpage = $postConfig['postsperpage'];
        }

        $output = "";
        foreach ($postlist as $post) {
            $output.= $this->generatePostTemplate($post, $postConfig, 'teaser');
        }

        // Pagination
        $response->setVar('postsperpage', $postsperpage);
        $response->setVar('currentPage', $pageNum);
        $response->setVar('totalnumposts', count($postlist));

        // Set Page Title
        $response->setTitle("Posts tagged with {$tag} - {$this->blog->name}");
        $response->setVar('userIsContributor', $isContributor);
        $response->setVar('tagName', $tag);
        $response->setVar('posts', $output);
        $response->setVar('paginator', new Pagination());
        $response->setVar('blog', $this->blog);
        $response->write('posts/postsbytag.tpl', 'BlogView');
    }
    
    /**
     * getFontFamilyFromName($fontName as String)
     * @return <string> CSS String for font-family rule
     */
    protected function getFontFamilyFromName($fontName)
    {
        $fontarray = [
            "ARIAL" => "Arial, Helvetica, sans-serif",
            "CALIBRI" => "Calibri, sans-serif",
            "COMICSANS" => "'Comic Sans MS', cursive",
            "COURIER" => "'Courier New', monospace",
            "IMPACT" => "Impact, Charcoal, sans-serif",
            "LUCIDA" => "'Lucida Console', Monaco, monospace",
            "TAHOMA" => 'Tahoma, Geneva, sans-serif',
            "TREBUCHET" => "'Trebuchet MS', sans-serif"
        ];

        if(array_key_exists($fontName, $fontarray)) return $fontarray[$fontName];
        else return "Arial, Helvetica, sans-serif";
    }
    
    public function getTemplateConfig()
    {
        $settings = file_get_contents(SERVER_PATH_BLOGS .'/'. $this->blog->id .'/template_config.json');
        return json_decode($settings, true);
    }
        
    /**
     * addView($postid as int)
     * Record that user has viewed the post
     */
    public function addView($postid)
    {
        $arrayVisitors = $this->modelPosts->getViewsByPost($postid);
        $userip = $_SERVER['REMOTE_ADDR'];
        $countUpdated = false;
                
        foreach($arrayVisitors as $visitor) {
            if($userip == $visitor['userip']) {
                $this->modelPosts->incrementUserView($postid, $userip);
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
     * View Individual Post
     */
    public function viewPost(&$request, &$response)
    {
        if (CUSTOM_DOMAIN) {
            $postUrl = $request->getUrlParameter(0);
        }
        else {
            $postUrl = $request->getUrlParameter(2);
        }

        // Check conditions in which the user is not allowed to view the post
        if($post = $this->modelPosts->getPostByURL($postUrl, $this->blogID)) {
            $isContributor = BlogCMS::$userGroup !== false;
            if (($post->draft == 1 || strtotime($post->timestamp) > time()) && !$isContributor) {
                $response->redirect($this->pathPrefix, 'Cannot view this post', 'error');
            }
        }
        else {
            $response->redirect($this->pathPrefix, 'Cannot find this post', 'error');
        }
        
        // Get all data required
        // if ($post['allowcomments']) {
        //     $response->setVar('comments', $this->modelComments->getCommentsByPost($post['id'], false));
        // }

        // Record the view
        $this->addView($post->id);

        $this->generatePostTemplate($post, null, 'full');

        $response->addScript('/resources/ace/ace.js');
        $response->setVar('mdContent', $mdContent);
        $response->setVar('previousPost', $this->modelPosts->getPreviousPost($this->blogID, $post->timestamp));
        $response->setVar('nextPost', $this->modelPosts->getNextPost($this->blogID, $post->timestamp));
        $response->setTitle($post->title);
        $response->write('posts/singlepost.tpl', 'BlogView');
    }
    
    /**
     * Output the post date based on user defined settings
     */
    protected function formatDateFromSettings($timestamp, $postSettings, $location)
    {
        $res = "";
        
        // Get Values
        $dateformat    = 'Y-m-d';
        $timeformat    = 'H:i:s';
        $timelocation  = 'footer';
        $datelocation  = 'footer';
        $dateprefix    = 'Posted on: ';
        $dateseperator = ' at ';

        if (getType($postSettings) == 'array') {
            if (isset($postSettings['dateformat']))    $dateformat    = $postSettings['dateformat'];
            if (isset($postSettings['timeformat']))    $timeformat    = $postSettings['timeformat'];
            if (isset($postSettings['timelocation']))  $timelocation  = $postSettings['timelocation'];
            if (isset($postSettings['datelocation']))  $datelocation  = $postSettings['datelocation'];
            if (isset($postSettings['dateprefix']))    $dateprefix    = $postSettings['dateprefix'];
            if (isset($postSettings['dateseperator'])) $dateseperator = $postSettings['dateseperator'];
        }
        
        if ($datelocation == $location) {
            // Show Date
            $res.= $dateprefix;
            $res.= date($dateformat, strtotime($timestamp));
        }
        
        if ($timelocation == $location) {
            // Show Time
            $res.= $dateseperator;
            $res.= date($timeformat, strtotime($timestamp));
        }
        
        return $res;
    }
    
    /**
     * trimContent
     * This function provides (needs improvement) a HTML friendly summary of post
     * content, ensuring that rather than blindly taking the first (200) characters
     * it does not cut a tag in half and leave invalid HTML.
     * 
     * @param string $fullcontent
     * @param mixed $charactersToShow
     * @param string $openTag
     * @param string $closeTag
     * 
     * @return string Summary of content
     * 
     * @todo check all open tags have been closed
     */
    protected function trimContent($fullcontent, $charactersToShow='all', $openTag='<', $closeTag='>')
    {
        if($charactersToShow !== "all") {
        
            // Number of characters has been limited
            $trimmedContent = substr($fullcontent, 0, $charactersToShow);
            $lastOpeningTag = strrpos($trimmedContent, $openTag);
            
            if($lastOpeningTag !== false) {
            
                // There has been a tag started (we thinks)
                $lastClosingTag = strrpos($trimmedContent, $closeTag);
                
                if($lastClosingTag === false || $lastOpeningTag > $lastClosingTag) {
                    
                    // Believe there is still an open tag
                    $nextClosingTag = strpos($fullcontent, $closeTag, $lastOpeningTag + 1);
                    $nextOpeningTag = strpos($fullcontent, $openTag, $lastOpeningTag + 1);
                    
                    if($nextOpeningTag !== false && $nextClosingTag !== false) {
                        if($nextClosingTag < $nextOpeningTag) {
                            $charactersToShow = $nextClosingTag + 1;
                        }
                    }
                    elseif($nextClosingTag !== false) {
                        // Choose to end the substr after the tag has finished
                        $charactersToShow = $nextClosingTag + 1;
                    }
                }
            }
            // Reapply Limit to X characters
            $trimmedContent = substr($fullcontent, 0, $charactersToShow);
            
            // Add continuation marks if actual length is more than summary
            if(strlen($fullcontent) > $charactersToShow && $charactersToShow > 0) $trimmedContent.= "...";
        }
        else {
            $trimmedContent = $fullcontent;
        }
        
        // Remove Whitespace and return answer
        return trim($trimmedContent);
    }

    /**
     * Add a comment to a blog post
     * 
     * @todo Check that the user hasn't submitted more than 5 comments in last 30 seconds?
     *   Or if the last X comments were from the same user? to prevent comment spamming
     */
    public function addComment(&$request, &$response)
    {
        $postID = $request->getInt('fld_postid', -1);
        $post = $this->modelPosts->getPostByID($postID, $this->blogID);
        $commentText = $request->getString('fld_comment');
        $currentUser = BlogCMS::session()->currentUser;

        if (!$currentUser) {
            $response->redirect("/blogs/{$this->blogID}", 'You must be logged in to add a comment', 'error');
        }

        if (!$post) {
            $response->redirect("/blogs/{$this->blogID}", 'Post not found', 'error');
        }
        
        if (!isset($commentText) || strlen($commentText) == 0) {
            $response->redirect("/blogs/{$this->blogID}/posts/{$post['link']}", 'Please enter a comment', 'error');
        }        
        
        // Check that post allows reader comments
        if ($post['allowcomments'] == 0) {
            $response->redirect("/blogs/{$this->blogID}/posts/{$post['link']}", 'Comments are not allowed here', 'error');
        }

        if ($this->modelComments->addComment($commentText, $post['id'], $this->blogID, $currentUser['id'])) {
            $response->redirect("/blogs/{$this->blogID}/posts/{$post['link']}", 'Comment submitted - awaiting approval', 'success');
        }
        else {
            $response->redirect("/blogs/{$this->blogID}/posts/{$post['link']}", 'Error adding comment', 'error');
        }
    }
}
