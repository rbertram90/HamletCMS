<?php
namespace rbwebdesigns\HamletCMS\BlogView\controller;

use Codeliner;
use rbwebdesigns\core\Sanitize;
use rbwebdesigns\core\Pagination;
use rbwebdesigns\core\JSONhelper;
use rbwebdesigns\HamletCMS\HamletCMS;
use rbwebdesigns\HamletCMS\HamletCMSResponse;
use rbwebdesigns\HamletCMS\GenericController;

/**
 * Class BlogContent handles the generation of output for
 * the front-end of the blog
 */
class BlogContent extends GenericController
{
    protected $modelBlogs;       // Blogs Model
    protected $modelPosts;       // Posts Model
    protected $modelUsers;       // Users Model

    /** @var \rbwebdesigns\HamletCMS\Blog\Blog */
    protected $blog;

    protected $blogID;           // ID of current blog
    protected $blogConfig;       // Config array of current blog
        
    public function __construct($blog_key)
    {
        // Instantiate models
        $this->modelBlogs = HamletCMS::model('\rbwebdesigns\HamletCMS\Blog\model\Blogs');
        $this->modelContributors = HamletCMS::model('\rbwebdesigns\HamletCMS\Contributors\model\Contributors');
        $this->modelPosts = HamletCMS::model('\rbwebdesigns\HamletCMS\BlogPosts\model\Posts');
        $this->modelUsers = HamletCMS::model('\rbwebdesigns\HamletCMS\UserAccounts\model\UserAccounts');
        
        // Cached information for this blog
        $this->blog          = $this->modelBlogs->getBlogById($blog_key);
        $this->blogID        = $blog_key;
        $this->blogConfig    = null;

        parent::__construct();
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
        $teaserResponse = new HamletCMSResponse();
        $teaserResponse->enableSecureMode();

        // Copy accross sub-set of variables from main template
        $teaserResponse->setVar('userIsContributor', $this->response->getVar('userIsContributor'));

        HamletCMS::runHook('runTemplate', ['template' => 'postTeaser', 'post' => &$post, 'config' => &$config]);

        // Check if blog template is overriding the teaser
        // todo - find this once and store in config?!
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
        $teaserResponse->setVar('blog', $this->blog);

        return $teaserResponse->write($templatePath, $source, false);
    }

    /**
     * Generate the output for a single post view
     * 
     * @param \rbwebdesigns\HamletCMS\BlogPosts\Post $post
     * @param array $config
     */
    public function generateSinglePost($post, $config)
    {
        $globalResponse = HamletCMS::response();

        // Copy accross sub-set of variables from main template
        $teaserResponse = new HamletCMSResponse();
        $teaserResponse->setVar('blog_root_url', $globalResponse->getVar('blog_root_url'));
        $teaserResponse->setVar('blog_file_dir', $globalResponse->getVar('blog_file_dir'));
        $teaserResponse->setVar('userIsContributor', $globalResponse->getVar('user_is_contributor'));
        $teaserResponse->setVar('userAuthenticated', $globalResponse->getVar('user_is_logged_in'));

        // Get custom content to be displayed in post
        // Example - number of comments
        $post->after = [];
        HamletCMS::runHook('runTemplate', [
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
     * @param \rbwebdesigns\HamletCMS\BlogPosts\Post $post
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
     * View blog homepage
     * 
     * Handles route: /
     */
    public function viewHome()
    {
        $blogConfig = $this->blog->config();

        if (isset($blogConfig['blog'])) {
            if ($blogConfig['blog']['use_post_as_homepage'] === 'on') {
                $postID = $blogConfig['blog']['homepage_post_id'];
                $post = $this->modelPosts->getPostById($postID);

                if ($post && $post->blog_id == $this->blog->id) {
                    return $this->viewPost($post);
                }
            }
        }

        $this->viewPostLister();
    }

    /**
     * Default blog home view
     */
    public function viewPostLister()
    {
        $blogConfig = $this->blog->config();
        $pageNum = $this->request->getInt('s', 1);
        $postConfig = null;
        $showsocialicons = 1;
        $summarylength = 150;
        $postsperpage = 5;
        $loadtype = 'paginated'; // loadmore

        if (isset($blogConfig['posts'])) {
            $postConfig = $blogConfig['posts'];
            if (isset($postConfig['loadtype'])) $loadtype = $postConfig['loadtype'];

            if (isset($postConfig['postsummarylength'])) $summarylength = $postConfig['postsummarylength'];
            if (isset($postConfig['postsperpage'])) $postsperpage = $postConfig['postsperpage'];
            if (!key_exists('extraclasses', $postConfig)) $postConfig['extraclasses'] = 'ui items';

            $this->response->setVar('postConfig', $postConfig);
        }

        $postlist = $this->modelPosts->getPostsByBlog($this->blogID, $pageNum, $postsperpage);
        $output = "";

        $isContributor = HamletCMS::$userGroup !== false;
        $this->response->setVar('userIsContributor', $isContributor);

        foreach ($postlist as $post) {
            $output.= $this->generatePostTemplate($post, $postConfig, 'teaser');
        }
        
        // Pagination
        $this->response->setVar('postsperpage', $postsperpage);
        $this->response->setVar('totalnumposts', $this->modelPosts->count(['blog_id' => $this->blogID]));

        $this->response->setTitle($this->blog->name);
        $this->response->setVar('loadtype', $loadtype);
        $this->response->setVar('posts', $output);
        $this->response->setVar('paginator', new Pagination());
        $this->response->setVar('blog', $this->blog);
        $this->response->write('posts/postshome.tpl', 'BlogView');
    }
        
    /**
     * Generate the HTML to be shown in the header
     * 
     * @return string html for header
     */
    public function generateHeader()
    {
        $headerResponse = new HamletCMSResponse();
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
        $headerResponse = new HamletCMSResponse();
        $headerResponse->setVar('user_is_contributor', $this->response->getVar('user_is_contributor'));
        $headerResponse->setVar('user', HamletCMS::session()->$currentUser);
        $headerResponse->setVar('blog', $this->blog);
        $headerResponse->setVar('widgets', $this->response->getVar('widgets'));

        return $headerResponse->write($templatePath, $source, false);
    }

    /**
     * Generate the HTML to be shown in the footer
     * 
     * @return string html for footer
     */
    public function generateFooter()
    {
        // Get the JSON blog config
        $blogConfig = $this->blog->config();
        
        // Check that the footer key exists
        if (!is_array($blogConfig) || !array_key_exists('footer', $blogConfig)) {
            return '';
        }
        
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

        $footerResponse = new HamletCMSResponse();
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
        $footerResponse = new HamletCMSResponse();
        $footerResponse->setVar('blog_root_url', $this->response->getVar('blog_root_url'));
        $footerResponse->setVar('blog_file_dir', $this->response->getVar('blog_file_dir'));
        $footerResponse->setVar('user_is_contributor', $this->response->getVar('user_is_contributor'));
        $footerResponse->setVar('user', HamletCMS::session()->$currentUser);
        $footerResponse->setVar('blog', $this->blog);
        $footerResponse->setVar('widgets', $this->response->getVar('widgets'));

        return $footerResponse->write($templatePath, $source, false) . $footerContent;
    }
    
    /**
     * Generate the CSS for the header background
     * 
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
                        
        return $headerContent;
    }
    
    public function viewPostsByAuthor() {
        $author_id = $this->request->getUrlParameter(2);
        $author = $this->modelUsers->getById($author_id);
        $pageNum = $this->request->getInt('s', 1);

        $isContributor = false;
        if ($currentUser = HamletCMS::session()->currentUser) {
            $isContributor = $this->modelContributors->isBlogContributor($currentUser['id'], $this->blogID);
        }

        $blogConfig = $this->blog->config();
        $postConfig = null;
        $postsperpage = 5;

        if (isset($blogConfig['posts'])) {
            $postConfig = $blogConfig['posts'];
            if (isset($postConfig['postsperpage'])) $postsperpage = $postConfig['postsperpage'];
            $this->response->setVar('postConfig', $postConfig);
        }

        $postlist = $this->modelPosts->getPostsByAuthor($this->blogID, $author_id, $postsperpage, $pageNum);
        $output = "";
        foreach ($postlist as $post) {
            $output.= $this->generatePostTemplate($post, $postConfig, 'teaser');
        }

        // Pagination
        $this->response->setVar('postsperpage', $postsperpage);
        $this->response->setVar('currentPage', $pageNum);
        $this->response->setVar('totalnumposts', $this->modelPosts->count(['blog_id' => $this->blogID, 'author_id' => $author_id]));

        // Set Page Title
        $this->response->setTitle("Posts tagged with {$tag} - {$this->blog->name}");
        $this->response->setVar('userIsContributor', $isContributor);
        $this->response->setVar('authorName', $author->name);
        $this->response->setVar('posts', $output);
        $this->response->setVar('paginator', new Pagination());
        $this->response->setVar('blog', $this->blog);
        $this->response->write('posts/postsbyauthor.tpl', 'BlogView');
    }

    /**
     * Show all posts matching tag
     * 
     * Handles route: /tags
     */
    public function viewPostsByTag()
    {
        $tag = $this->request->getUrlParameter(2);
        $pageNum = $this->request->getInt('s', 1);
        $postlist = $this->modelPosts->getBlogPostsByTag($this->blogID, $tag);

        $isContributor = false;
        if ($currentUser = HamletCMS::session()->currentUser) {
            $isContributor = $this->modelContributors->isBlogContributor($currentUser['id'], $this->blogID);
        }

        $blogConfig = $this->blog->config();
        $postConfig = null;
        $summarylength = 150;
        $postsperpage = 5;

        if (isset($blogConfig['posts'])) {
            $postConfig = $blogConfig['posts'];
            if (isset($postConfig['postsperpage'])) $postsperpage = $postConfig['postsperpage'];
            $this->response->setVar('postConfig', $postConfig);
        }

        $output = "";
        foreach ($postlist as $post) {
            $output.= $this->generatePostTemplate($post, $postConfig, 'teaser');
        }

        // Pagination
        $this->response->setVar('postsperpage', $postsperpage);
        $this->response->setVar('currentPage', $pageNum);
        $this->response->setVar('totalnumposts', count($postlist));

        // Set Page Title
        $this->response->setTitle("Posts tagged with {$tag} - {$this->blog->name}");
        $this->response->setVar('userIsContributor', $isContributor);
        $this->response->setVar('tagName', $tag);
        $this->response->setVar('posts', $output);
        $this->response->setVar('paginator', new Pagination());
        $this->response->setVar('blog', $this->blog);
        $this->response->write('posts/postsbytag.tpl', 'BlogView');
    }
    
    /**
     * Get template configuration data from file
     * 
     * @return array
     */
    public function getTemplateConfig()
    {
        $settings = file_get_contents(SERVER_PATH_BLOGS .'/'. $this->blog->id .'/template_config.json');
        return json_decode($settings, true);
    }
    
    /**
     * Increment the view count for a post
     * 
     * @param int $postid
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
    
    protected function ajaxLoadPosts() {
        $output = '';
        $blogConfig = $this->blog->config();
        $postsperpage = 5;

        if (isset($blogConfig['posts'])) {
            $postConfig = $blogConfig['posts'];
            if (isset($postConfig['loadtype'])) $loadtype = $postConfig['loadtype'];

            if (isset($postConfig['postsummarylength'])) $summarylength = $postConfig['postsummarylength'];
            if (isset($postConfig['postsperpage'])) $postsperpage = $postConfig['postsperpage'];
            if (!key_exists('extraclasses', $postConfig)) $postConfig['extraclasses'] = 'ui items';

            $this->response->setVar('postConfig', $postConfig);
        }

        $pageNum = $this->request->getInt('page', 1);
        $postlist = $this->modelPosts->getPostsByBlog($this->blogID, $pageNum, $postsperpage);

        foreach ($postlist as $post) {
            $output.= $this->generatePostTemplate($post, $postConfig, 'teaser');
        }

        $this->response->setBody($output);
        $this->response->writeBody();
        exit;
    }

    /**
     * Handle routes:
     *   /posts
     *   /posts/{post-url}
     */
    public function viewPost($post = null)
    {
        if (is_null($post)) {
            $postUrl = $this->request->getUrlParameter(CUSTOM_DOMAIN ? 0 : 2);

            if ($postUrl == 'loadmore') {
                // Ajax call to load next N posts
                return $this->ajaxLoadPosts();
            }

            if (!$postUrl) {
                return $this->viewPostLister();
            }
    
            $post = $this->modelPosts->getPostByURL($postUrl, $this->blogID);
        }
        
        if (!$post) {
            $response->redirect($this->blog->url(), 'Cannot find this post', 'error');
        }

        // Check access
        $isContributor = HamletCMS::$userGroup !== false;

        // Is the post still a draft or not scheduled to be released yet
        if (!$post->isPublic() && !$isContributor) {
            $this->response->redirect($this->blog->url(), 'Cannot view this post', 'error');
        }
        
        // Record the post view
        $this->addView($post->id);

        $this->generatePostTemplate($post, null, 'full');

        $this->response->setTitle($post->title);

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
