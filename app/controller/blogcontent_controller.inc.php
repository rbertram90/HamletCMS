<?php
namespace rbwebdesigns\blogcms;

use Codeliner;
use Michelf\Markdown;
use rbwebdesigns\core\Sanitize;
use rbwebdesigns\core\Pagination;
use rbwebdesigns\core\JSONhelper;

/**
 * blog_content_controller
 * this class is for front end actions on the blogs within blog_cms
 * i.e. viewing posts, making comments etc.
 */
class BlogContentController 
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
    
    public $header_hideTitle = false;
    public $header_hideDescription = false;
    
    public function __construct($blog_key)
    {
        $currentUser = BlogCMS::session()->currentUser;

        // Create Models
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\model\Blogs');
        $this->modelContributors = BlogCMS::model('\rbwebdesigns\blogcms\model\Contributors');
        $this->modelPosts = BlogCMS::model('\rbwebdesigns\blogcms\model\Posts');
        $this->modelComments = BlogCMS::model('\rbwebdesigns\blogcms\model\Comments');
        $this->modelUsers = BlogCMS::model('\rbwebdesigns\blogcms\model\AccountFactory');
        
        // Cached information for this blog
        $this->blog          = $this->modelBlogs->getBlogById($blog_key);
        $this->blogID        = $blog_key;
        $this->blogConfig    = null;
        $this->blogPostCount = null;

        if (CUSTOM_DOMAIN) {
            $this->pathPrefix = '';
        }
        else {
            $this->pathPrefix = "/blogs/{$this->blogID}";
        }
        
        // todo: Update this...
        if ($this->modelContributors->isBlogContributor($currentUser, $blog_key)) {
            $this->userPermissionsLevel = 2;
        }
        elseif ($this->userIsContributor()) {
            $this->userPermissionsLevel = 1;
        }
        else {
            $this->userPermissionsLevel = 0;
        }
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
     *  Check if the current user is a contributor of this blog
     *  @return bool true if user is a contributor, false otherwise
     */
    public function userIsContributor()
    {
        $currentUser = BlogCMS::session()->currentUser;
        if(!$currentUser) return false;
        return $this->modelContributors->isBlogContributor($currentUser, $this->blogID);
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
     *  View blog homepage
     *  @return <array> data for template
     */
    public function viewHome(&$request, &$response)
    {
        $pageNum = $request->getInt('s', 1);

        $isContributor = false;
        if ($currentUser = BlogCMS::session()->currentUser) {
            $isContributor = $this->modelContributors->isBlogContributor($currentUser['id'], $this->blogID);
        }

        $blogConfig = $this->getBlogConfig($this->blogID);
        $postConfig = null;
        $showTags = 1;
        $shownumcomments = 1;
        $showsocialicons = 1;
        $summarylength = 150;
        $postsperpage = 5;

        if (isset($blogConfig['posts'])) {
            $postConfig = $blogConfig['posts'];
            if (isset($postConfig['showtags']))     $showtags = $postConfig['showtags'];
            if (isset($postConfig['shownumcomments'])) $shownumcomments = $postConfig['shownumcomments'];
            if (isset($postConfig['showsocialicons'])) $showsocialicons = $postConfig['showsocialicons'];
            if (isset($postConfig['postsummarylength'])) $summarylength = $postConfig['postsummarylength'];
            if (isset($postConfig['postsperpage'])) $postsperpage = $postConfig['postsperpage'];
        }

        $postlist = $this->modelPosts->getPostsByBlog($this->blogID, $pageNum, $postsperpage);

        $response->setVar('showtags', $showTags);
        $response->setVar('shownumcomments', $shownumcomments);
        $response->setVar('showsocialicons', $showsocialicons);
        $response->setVar('postsperpage', $postsperpage);
        $response->setVar('currentPage', $pageNum);

        $response->setVar('totalnumposts', $this->modelPosts->count(['blog_id' => $this->blogID]));

        // Format content
        for ($p = 0; $p < count($postlist); $p++) {

            if ($postlist[$p]['type'] == 'layout') {
                $layout = JSONHelper::JSONtoArray($postlist[$p]['content']);
                $mdContent = $this->generateLayoutMarkup($layout);
                $postlist[$p]['trimmedContent'] = $mdContent;
            }
            else {
                $mdContent = Markdown::defaultTransform($postlist[$p]['content']);
                $postlist[$p]['trimmedContent'] = $this->trimContent($mdContent, $summarylength);
            }

            if (strlen($postlist[$p]['tags']) > 0) {
                $postlist[$p]['tags'] = explode(',', $postlist[$p]['tags']);
            }
            else {
                $postlist[$p]['tags'] = [];
            }
            
            $postlist[$p]['headerDate'] = $this->formatDateFromSettings($postlist[$p]['timestamp'], $postConfig, 'title');
            $postlist[$p]['footerDate'] = $this->formatDateFromSettings($postlist[$p]['timestamp'], $postConfig, 'footer');

            if ($postlist[$p]['type'] == 'gallery') {
                $postlist[$p]['images'] = explode(',', $postlist[$p]['gallery_imagelist']);
            }
        }

        $response->setTitle($this->blog['name']);
        $response->setVar('userIsContributor', $isContributor);
        $response->setVar('posts', $postlist);
        $response->setVar('paginator', new Pagination());
        $response->setVar('blog', $this->blog);
        $response->write('blog/posts/postshome.tpl');

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
        echo $this->blog['pagelist'];
    }

    /**
     * Each link is a blog post which has been marked as a 'page' through the settings menus
     * @return string html for a top navigation bar
    **/
    public function generateNavigation()
    {
        if (!array_key_exists('pagelist', $this->blog) || strlen($this->blog['pagelist']) == 0) {
            return '';
        }
        $navigation = '';
        $pagelist = explode(',', $this->blog['pagelist']);

        foreach ($pagelist as $postid) {
            if (is_numeric($postid)) {
                $arrayPosts = $this->modelPosts->get('*', array('id' => $postid));
                $arrayPost = $arrayPosts[0];
                $navigation.= '<a href="'.$this->pathPrefix.'/posts/'.$arrayPost['link'].'" class="item">'.$arrayPost['title'].'</a>';
            }
            elseif (substr($postid, 0, 2) == 't:') {
                $tag = substr($postid, 2);
                $navigation.= '<a href="'.$this->pathPrefix.'/tags/'.$tag.'" class="item">'.ucfirst($tag).'</a>';
            }
        }
        return $navigation;
    }
    
    /**
     * New widget generator
     * @todo complete
     */
    public function generateWidgets()
    {
        $widgetConfigPath = SERVER_PATH_BLOGS . '/' . $this->blog['id'] . '/widgets.json';
        $widgets = [];

        if(!file_exists($widgetConfigPath)) return '';
        
        $widgetsConfig = JSONhelper::JSONFileToArray($widgetConfigPath);

        $config = BlogCMS::config();
        if (CUSTOM_DOMAIN) {
            $cmsDomain = $config['environment']['canonical_domain'];
            $pathPrefix = '';
        }
        else {
            $cmsDomain = '';
            $pathPrefix = "/blogs/{$this->blog['id']}";
        }

        $widgetSmarty = new \Smarty;
        $widgetSmarty->setTemplateDir(SERVER_PATH_WIDGETS);

        foreach ($widgetsConfig as $section => $childWidgets) {
            $section = strtolower($section);
            $widgets[$section] = '';
            foreach ($childWidgets as $name => $widget) {
                $widgetSmarty->clearAllAssign();
                foreach ($widget as $settingkey => $settingvalue) {
                    $widgetSmarty->assign($settingkey, $settingvalue);
                }
                $widgetSmarty->assign('blog', $this->blog);
                $widgetSmarty->assign('cms_url', $cmsDomain);
                $widgetSmarty->assign('blog_root_url', $pathPrefix);
                $widgets[$section] .= $widgetSmarty->fetch($name . '/view.tpl');
            }
        }

        return $widgets;
    }
    
    /**
     * Generate the HTML to be shown in the footer
     * @return string html
     */
    public function generateFooter()
    {
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

        $blogConfig = $this->getBlogConfig($this->blogID);
        $postConfig = null;
        $showTags = 1;
        $shownumcomments = 1;
        $showsocialicons = 1;
        $summarylength = 150;
        $postsperpage = 5;

        if (isset($blogConfig['posts'])) {
            $postConfig = $blogConfig['posts'];
            if (isset($postConfig['showtags']))     $showtags = $postConfig['showtags'];
            if (isset($postConfig['shownumcomments'])) $shownumcomments = $postConfig['shownumcomments'];
            if (isset($postConfig['showsocialicons'])) $showsocialicons = $postConfig['showsocialicons'];
            if (isset($postConfig['postsummarylength'])) $summarylength = $postConfig['postsummarylength'];
            if (isset($postConfig['postsperpage'])) $postsperpage = $postConfig['postsperpage'];
        }

        $response->setVar('showtags', $showTags);
        $response->setVar('shownumcomments', $shownumcomments);
        $response->setVar('showsocialicons', $showsocialicons);
        $response->setVar('postsperpage', $postsperpage);
        $response->setVar('currentPage', $pageNum);

        $response->setVar('totalnumposts', count($postlist));

        // Format content
        for ($p = 0; $p < count($postlist); $p++) {

            if ($postlist[$p]['type'] == 'layout') {
                $layout = JSONHelper::JSONtoArray($postlist[$p]['content']);
                $mdContent = $this->generateLayoutMarkup($layout);
                $postlist[$p]['trimmedContent'] = $mdContent;

            }
            else {
                $mdContent = Markdown::defaultTransform($postlist[$p]['content']);
                $postlist[$p]['trimmedContent'] = $this->trimContent($mdContent, $summarylength);
            }

            $postlist[$p]['trimmedContent'] = $this->trimContent($mdContent, $summarylength);
            $postlist[$p]['tags'] = explode(',', $postlist[$p]['tags']);

            $postlist[$p]['headerDate'] = $this->formatDateFromSettings($postlist[$p]['timestamp'], $postConfig, 'title');
            $postlist[$p]['footerDate'] = $this->formatDateFromSettings($postlist[$p]['timestamp'], $postConfig, 'footer');

            if ($postlist[$p]['type'] == 'gallery') {
                $postlist[$p]['images'] = explode(',', $postlist[$p]['gallery_imagelist']);
            }
        }

        // Set Page Title
        $response->setTitle("Posts tagged with {$tag} - {$this->blog['name']}");
        $response->setVar('userIsContributor', $isContributor);
        $response->setVar('tagName', $tag);
        $response->setVar('posts', $postlist);
        $response->setVar('paginator', new Pagination());
        $response->setVar('blog', $this->blog);
        $response->write('blog/posts/postsbytag.tpl');
    }

    /**
     * Get the blog config file 'config.json' as an array
     */
    private function getBlogConfig($blogid)
    {
        // Check variable cache
        if($this->blogConfig === null) {
            $settings = file_get_contents(SERVER_PATH_BLOGS.'/'.$blogid.'/config.json');
            $this->blogConfig = json_decode($settings, true);
        }
        return $this->blogConfig;
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
        $lsSettings = file_get_contents(SERVER_PATH_BLOGS.'/'.$this->blog['id'].'/template_config.json');
        return json_decode($lsSettings, true);
    }
    
    /**
     * Generates the CSS for a blog specified in the JSON file 'template_config.json'
     * which should exist under the $pblogid folder
     */
    public function getBlogCustomCSS()
    {
        $lobjSettings = $this->getTemplateConfig();
        $css = "";
        
        if(count($lobjSettings) == 0) return;
        
        foreach($lobjSettings as $key => $lobjClass):
            $key = strtolower($key);
            if($key == 'layout' || $key == 'includes') continue;
        
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
            $isContributor = false;

            if ($currentUser = BlogCMS::session()->currentUser) {
                $isContributor = $this->modelContributors->isBlogContributor($currentUser['id'], $this->blogID);
            }
            if (($post['draft'] == 1 || strtotime($post['timestamp']) > time()) && !$isContributor) {
                $response->redirect($this->pathPrefix, 'Cannot view this post', 'error');
            }
        }
        else {
            $response->redirect($this->pathPrefix, 'Cannot find this post', 'error');
        }
        
        // Get all data required
        if ($post['allowcomments']) {
            $response->setVar('comments', $this->modelComments->getCommentsByPost($post['id'], false));
        }

        // Apply post configuration
        $blogConfig = $this->getBlogConfig($this->blogID);
        $showtags = $showsocialicons = 1;

        if (isset($blogConfig['posts'])) {
            $postConfig = $blogConfig['posts'];
            if (isset($postConfig['showtags'])) $showtags = $postConfig['showtags'];
            if (isset($postConfig['showsocialicons'])) $showsocialicons = $postConfig['showsocialicons'];
            $response->setVar('headerDate', $this->formatDateFromSettings($post['timestamp'], $postConfig, 'title'));
            $response->setVar('footerDate', $this->formatDateFromSettings($post['timestamp'], $postConfig, 'footer'));
        }
        else {
            $response->setVar('headerDate', '');
            $response->setVar('footerDate', $this->formatDateFromSettings($post['timestamp'], null, 'footer'));
        }

        // Record the view
        $this->addView($post['id']);

        $response->setVar('post', $post);
        $response->setVar('showtags', $showtags);
        $response->setVar('showsocialicons', $showsocialicons);
        $response->setVar('userAuthenticated', getType($currentUser) == 'array');
        $response->setVar('userIsContributor', $isContributor);

        if ($post['type'] == 'layout') {
            $layout = JSONHelper::JSONtoArray($post['content']);
            $mdContent = $this->generateLayoutMarkup($layout);
        }
        else {
            $mdContent = Markdown::defaultTransform($post['content']);
        }

        $response->setVar('mdContent', $mdContent);
        $response->setVar('previousPost', $this->modelPosts->getPreviousPost($this->blogID, $post['timestamp']));
        $response->setVar('nextPost', $this->modelPosts->getNextPost($this->blogID, $post['timestamp']));
        $response->setTitle($post['title']);
        $response->write('blog/posts/singlepost.tpl');
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
     * generateLayoutMarkup
     */
    protected function generateLayoutMarkup($array)
    {
        $out = "<div class='ui grid'>";

        foreach ($array['rows'] as $row) {
            $rowClasses = "";
            $rOut = "";
            $columnWidths = null;

            switch ($row['columnLayout']) {
                case "twoColumns_50":
                    $rowClasses = "two column";
                    break;

                case "twoColumns_75":
                    $columnWidths = [75, 25];
                    break;

                case "twoColumns_75":
                    $columnWidths = [25, 75];
                    break;

                case "twoColumns_66":
                    $columnWidths = [66, 33];
                    break;

                case "twoColumns_66":
                    $columnWidths = [33, 66];
                    break;

                case "threeColumns":
                    $rowClasses = "three column";
                    break;

                case "fourColumns":
                    $rowClasses = "four column";
                    break;

                default:
                case "singleColumn":
                    $columnWidths = [100];
                    break;
            }

            foreach ($row['columns'] as $c => $column) {

                $classes = "";
                if ($columnWidths) {
                    switch ($columnWidths[$c]) {
                        case 100: $classes = "sixteen wide"; break;
                        case 75: $classes = "twelve wide"; break;
                        case 66: $classes = "ten wide"; break;
                        case 33: $classes = "six wide"; break;
                        case 25: $classes = "four wide"; break;
                    }
                }

                $style = '';
                if (isset($column['backgroundColour'])) {
                    $classes .= ' ' . $column['backgroundColour'];
                }

                if (isset($column['fontColour'])) {
                    $style .= sprintf('color: %s;', $column['fontColour']);
                }

                if ($column['image']) {
                    $classes .= ' black image-column';
                    $style.= 'background-image: url(/blogdata/' . $this->blogID . '/images/' . $column['image'] . ');';
                }
                if ($column['minimumHeight']) {
                    $style.= 'min-height: '. $column['minimumHeight'] .';';
                }

                $rOut.= sprintf("<div class='%s column' style='%s'>", $classes, $style);

                if ($column['textContent']) {
                    $rOut.= nl2br($column['textContent']);
                }

                $rOut.= "</div>";
            }

            $out .= sprintf("<div class='%s row'>%s</div>", $rowClasses, $rOut);
        }

        $out .= '</div>';

        return $out;
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
