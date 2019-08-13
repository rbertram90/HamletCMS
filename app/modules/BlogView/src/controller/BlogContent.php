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
    protected $modelUsers;       // Users Model
    protected $blog;             // Current viewing blog
    protected $blogID;           // ID of current blog
    protected $blogConfig;       // Config array of current blog
    protected $request;
    protected $response;
    
    public $header_hideTitle = false;
    public $header_hideDescription = false;
    
    public function __construct($blog_key)
    {
        // Instantiate models
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\Blog\model\Blogs');
        $this->modelContributors = BlogCMS::model('\rbwebdesigns\blogcms\Contributors\model\Contributors');
        $this->modelPosts = BlogCMS::model('\rbwebdesigns\blogcms\BlogPosts\model\Posts');
        $this->modelUsers = BlogCMS::model('\rbwebdesigns\blogcms\UserAccounts\model\UserAccounts');
        
        // Cached information for this blog
        $this->blog          = $this->modelBlogs->getBlogById($blog_key);
        $this->blogID        = $blog_key;
        $this->blogConfig    = null;

        if (CUSTOM_DOMAIN) {
            $this->pathPrefix = '';
            $this->fileDir = '';
        }
        else {
            $this->pathPrefix = "/blogs/{$this->blogID}";
            $this->fileDir = "/blogdata/{$this->blogID}";
        }

        $this->request = BlogCMS::request();
        $this->response = BlogCMS::response();
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
        $globalResponse = BlogCMS::response();

        // Copy accross sub-set of variables from main template
        $teaserResponse = new BlogCMSResponse();
        $teaserResponse->setVar('blog_root_url', $globalResponse->getVar('blog_root_url'));
        $teaserResponse->setVar('blog_file_dir', $globalResponse->getVar('blog_file_dir'));
        $teaserResponse->setVar('userIsContributor', $globalResponse->getVar('user_is_contributor'));
        $teaserResponse->setVar('userAuthenticated', $globalResponse->getVar('user_is_logged_in'));

        // Get custom content to be displayed in post
        // Example - number of comments
        $post->after = [];
        BlogCMS::runHook('runTemplate', [
            'template' => 'singlePost',
            'post' => &$post,
            'config' => &$config,
            'response' => $teaserResponse
        ]);

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
        $teaserResponse->setVar('blog', $this->blog);

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
     * Generate the HTML to be shown in the header
     */
    public function generateHeader()
    {
        $headerResponse = new BlogCMSResponse();
        $headerTemplate = SERVER_PATH_BLOGS .'/'. $this->blogID .'/templates/header.tpl';
        // Check if blog template is overriding the teaser
        // @todo - find this once and store in config?!
        if (file_exists($headerTemplate)) {
            $templatePath = 'file:'. $headerTemplate;
            $source = '';
        }
        else {
            // Use system default
            $templatePath = 'header.tpl';
            $source = 'BlogView';
        }

        // Copy accross sub-set of variables from main template
        $headerResponse = new BlogCMSResponse();
        $headerResponse->setVar('blog_root_url', $this->response->getVar('blog_root_url'));
        $headerResponse->setVar('blog_file_dir', $this->response->getVar('blog_file_dir'));
        $headerResponse->setVar('user_is_contributor', $this->response->getVar('user_is_contributor'));
        $headerResponse->setVar('user', BlogCMS::session()->$currentUser);
        $headerResponse->setVar('blog', $this->blog);
        $headerResponse->setVar('hide_title', $this->header_hideTitle);
        $headerResponse->setVar('hide_description', $this->header_hideDescription);
        $headerResponse->setVar('widgets', $this->response->getVar('widgets'));

        return $headerResponse->write($templatePath, $source, false);
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
        if(!is_array($blogConfig) || !array_key_exists('footer', $blogConfig)) return '';
        
        // Get data from config
        $configReader = new Codeliner\ArrayReader\ArrayReader($blogConfig);
        $backgroundImage = $configReader->stringValue('footer.background_image', false); // Background Image
        
        // Generate Background CSS
        if ($backgroundImage && strlen($blogConfig['footer']['background_image']) > 0) {
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

        $footerResponse = new BlogCMSResponse();
        $footerTemplate = SERVER_PATH_BLOGS .'/'. $this->blogID .'/templates/footer.tpl';
        // Check if blog template is overriding the teaser
        // @todo - find this once and store in config?!
        if (file_exists($footerTemplate)) {
            $templatePath = 'file:'. $footerTemplate;
            $source = '';
        }
        else {
            // Use system default
            $templatePath = 'footer.tpl';
            $source = 'BlogView';
        }

        // Copy accross sub-set of variables from main template
        $footerResponse = new BlogCMSResponse();
        $footerResponse->setVar('blog_root_url', $this->response->getVar('blog_root_url'));
        $footerResponse->setVar('blog_file_dir', $this->response->getVar('blog_file_dir'));
        $footerResponse->setVar('user_is_contributor', $this->response->getVar('user_is_contributor'));
        $footerResponse->setVar('user', BlogCMS::session()->$currentUser);
        $footerResponse->setVar('blog', $this->blog);
        $footerResponse->setVar('widgets', $this->response->getVar('widgets'));

        return $footerResponse->write($templatePath, $source, false) . $footerContent;
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
                
        foreach ($arrayVisitors as $visitor) {
            if ($userip == $visitor['userip']) {
                $this->modelPosts->incrementUserView($postid, $userip);
                $countUpdated = true;
                break;
            }
        }
        
        if (!$countUpdated) {
            // New Visitor
            $this->modelPosts->recordUserView($postid, $userip);
        }
    }
    
    /**
     * View Individual Post
     */
    public function viewPost(&$request, &$response)
    {
        $postUrl = $request->getUrlParameter(CUSTOM_DOMAIN ? 0 : 2);

        $post = $this->modelPosts->getPostByURL($postUrl, $this->blogID);
        
        if (!$post) {
            $response->redirect($this->pathPrefix, 'Cannot find this post', 'error');
        }

        // Check access
        $isContributor = BlogCMS::$userGroup !== false;

        // Is the post still a draft or not scheduled to be released yet
        if (!$post->isPublic() && !$isContributor) {
            $response->redirect($this->pathPrefix, 'Cannot view this post', 'error');
        }
        
        // Record the post view
        $this->addView($post->id);

        $this->generatePostTemplate($post, null, 'full');

        $response->setTitle($post->title);
        // $response->setVar('previousPost', $this->modelPosts->getPreviousPost($this->blogID, $post->timestamp));
        // $response->setVar('nextPost', $this->modelPosts->getNextPost($this->blogID, $post->timestamp));
        // $response->addScript('/resources/ace/ace.js');
        // $response->setVar('mdContent', $mdContent);
        // $response->write('posts/singlepost.tpl', 'BlogView');
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

}
