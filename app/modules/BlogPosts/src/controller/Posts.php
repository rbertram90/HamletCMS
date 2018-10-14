<?php
namespace rbwebdesigns\blogcms\BlogPosts\controller;

use rbwebdesigns\blogcms\Contributors\model\ContributorGroups;
use rbwebdesigns\core\Sanitize;
use rbwebdesigns\core\AppSecurity;
use rbwebdesigns\blogcms\GenericController;
use rbwebdesigns\blogcms\BlogCMS;
use Codeliner\ArrayReader\ArrayReader;

/**
 * class Posts
 * 
 * This is the controller which acts as the intermediatory between the
 * model (database) and the view. Any requests to the model are sent from
 * here rather than the view.
 * 
 * Routes:
 *  /posts/manage/<blogid>
 *  /posts/create/<blogid>
 *  /posts/create/<blogid>/standard
 *  /posts/edit/<postid>
 *  /posts/delete/<postid>
 * 
 * @author R Bertram <ricky@rbwebdesigns.co.uk>
 */
class Posts extends GenericController
{
    /**
     * @var \rbwebdesigns\blogcms\BlogPosts\model\Posts
     */
    protected $model;
    /**
     * @var \rbwebdesigns\blogcms\Blog\model\Blogs
     */
    protected $modelBlogs;
    /**
     * @var \rbwebdesigns\blogcms\Contributors\model\Contributors
     */
    protected $modelContributors;
    /**
     * @var \rbwebdesigns\core\Request
     */
    protected $request;
    /**
     * @var \rbwebdesigns\core\Response
     */
    protected $response;
    /**
     * @var array Active blog
     */
    protected $blog = null;
    /**
     * @var array Active post
     */
    protected $post = null;
    

    public function __construct()
    {
        $this->model = BlogCMS::model('\rbwebdesigns\blogcms\BlogPosts\model\Posts');
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\Blog\model\Blogs');
        $this->modelContributors = BlogCMS::model('\rbwebdesigns\blogcms\Contributors\model\Contributors');
        $this->modelPermissions = BlogCMS::model('\rbwebdesigns\blogcms\Contributors\model\Permissions');

        BlogCMS::$activeMenuLink = 'posts';

        $this->request = BlogCMS::request();
        $this->response = BlogCMS::response();

        $this->setup();
    }
    
    /**
     * Setup controller
     * 
     * 1. Gets the key records that will be used for any request to keep
     *    the code DRY (Blog and Post)
     * 
     * 2. Checks the user has permissions to run the request
     */
    protected function setup()
    {
        $currentUser = BlogCMS::session()->currentUser;
        $action = $this->request->getUrlParameter(0);
        $access = true;

        if (!BlogCMS::$blogID) {
            $postID = $this->request->getUrlParameter(1);
            $this->post = $this->model->getPostById($postID);
            BlogCMS::$blogID = $this->post['blog_id'];
        }

        $this->blog = BlogCMS::getActiveBlog();

        // Check the user is a contributor of the blog to begin with
        if (!$this->modelContributors->isBlogContributor($currentUser['id'], $this->blog['id'])) {
            $access = false;
        }

        // Check action specific permissions
        switch ($action) {
            case 'edit':
                if ($this->post['author_id'] != $currentUser['id']) {
                    $access = $this->modelPermissions->userHasPermission($this->blog['id'], 'edit_all_posts');
                }
                elseif ($this->request->method() == 'POST' && $this->request->getInt('fld_draft') == 0) {
                    $access = $this->modelPermissions->userHasPermission($this->blog['id'], 'publish_posts');
                }
                break;

            case 'create':
                if ($this->request->method() == 'POST' && $this->request->getInt('fld_draft') == 0) {
                    $access = $this->modelPermissions->userHasPermission($this->blog['id'], 'publish_posts');
                }
                else {
                    $access = $this->modelPermissions->userHasPermission($this->blog['id'], 'create_posts');
                }
                break;

            case 'delete':
                $access = $this->modelPermissions->userHasPermission($this->blog['id'], 'delete_posts');
                break;
        }

        if (!$access) {
            $this->response->redirect('/cms', '403 Access Denied', 'error');
        }
    }

    /**
     * Handles /posts/manage/<blogid>
     * Most of the heavy data is done in a seperate ajax call
     */
    public function manage()
    {
        $this->response->setVar('blog', $this->blog);
        $this->response->setTitle('Manage Posts - ' . $this->blog['name']);
        $this->response->addScript('/js/showUserCard.js');
        $this->response->write('manage.tpl', 'BlogPosts');
    }
    
    /**
     * Handles POST /posts/create/<blogid>
     */
    public function create()
    {
        if ($this->request->method() == 'POST') return $this->runCreatePost();

        $this->response->setVar('blog', $this->blog);
        $this->response->setTitle('New Post');
                
        switch ($this->request->getUrlParameter(2)) {
            case 'video':
            $this->response->write('posts/videopost.tpl');
            break;
            
            case 'gallery':
            $this->response->write('posts/gallerypost.tpl');
            break;
            
            case 'standard':
            $this->response->write('posts/standardpost.tpl');
            break;

            case 'layout':

            $imagesHTML = '';
            $path = SERVER_ROOT . "/app/public/blogdata/" . $this->blog['id'] . "/images";

            if (is_dir($path)) {
                if ($handle = opendir($path)) {
                    while (false !== ($file = readdir($handle))) {
                        $ext = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
                        $filename = pathinfo($file, PATHINFO_FILENAME);
            
                        if($ext == 'JPG' || $ext == 'PNG' || $ext == 'GIF' || $ext == 'JPEG') {
                            $imagesHTML .= '<img src="/blogdata/'. $this->blog['id'] .'/images/'. $file .'" height="100" width="" data-name="'. $filename .'" class="selectableimage" />';
                        }
                    }
                    closedir($handle);
                }
            }
    
            $this->response->setVar('imagesOutput', $imagesHTML);


            $this->response->addScript('/js/layoutPost.js');
            $this->response->addStylesheet('/css/layoutPost.css');
            $this->response->write('layoutpost.tpl', 'BlogPosts');
            break;
            
            default:
            $newPostMenu = new Menu('newpost');
            BlogCMS::runHook('onGenerateMenu', ['id' => 'newpost', 'menu' => &$newPostMenu]);

            $this->response->setVar('menu', $newPostMenu->getLinks());
            $this->response->write('newpostmenu.tpl', 'BlogPosts');
            break;
        }
    }
    
    /**
     * Handles GET /posts/edit/<postID>
     */
    public function edit()
    {
        if ($this->request->method() == 'POST') return $this->runEditPost();
        
        if ($this->post['type'] == 'gallery') {
            $this->post['gallery_imagelist'] = explode(',', $this->post['gallery_imagelist']);
        }

        if ($this->post['initialautosave'] == 1) {
            // get the most recent content from the actual autosave record
            $autosave = $this->model->getAutosave($this->post['id']);

            if (getType($autosave) == 'array') {
                $this->post = array_merge($this->post, $autosave);
            }

            $this->model->update(['id' => $this->post['id']], ['initialautosave' => 0]);
        }
        elseif ($this->model->autosaveExists($this->post['id'])) {
            // Has been edited without being saved
            $this->post['autosave'] = $this->model->getAutosave($this->post['id']);
        }
        
        $this->response->setVar('post', $this->post);
        $this->response->setVar('blog', $this->blog);
        $this->response->setTitle('Edit Post - ' . $this->post['title']);
        
        // @todo This will change to be extenable (module for each type)
        switch ($this->post['type']) {
            case 'video':
            $this->response->write('videopost.tpl', 'BlogPosts');
            break;
            
            case 'gallery':
            $this->response->write('gallerypost.tpl', 'BlogPosts');
            break;
            
            case 'layout':

            $imagesHTML = '';
            $path = SERVER_ROOT . "/app/public/blogdata/" . $this->blog['id'] . "/images";

            if (is_dir($path)) {
                if ($handle = opendir($path)) {
                    while (false !== ($file = readdir($handle))) {
                        $ext = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
                        $filename = pathinfo($file, PATHINFO_FILENAME);
            
                        if($ext == 'JPG' || $ext == 'PNG' || $ext == 'GIF' || $ext == 'JPEG') {
                            $imagesHTML .= '<img src="/blogdata/'. $this->blog['id'] .'/images/'. $file .'" height="100" width="" data-name="'. $filename .'" class="selectableimage" />';
                        }
                    }
                    closedir($handle);
                }
            }
    
            $this->response->setVar('imagesOutput', $imagesHTML);

            $this->response->addScript('/js/layoutPost.js');
            $this->response->addStylesheet('/css/layoutPost.css');
            $this->response->write('layoutpost.tpl', 'BlogPosts');
            break;

            default:
            $this->response->write('standardpost.tpl', 'BlogPosts');
            break;
        }
    }
    
    /**
     * Handles POST /posts/create/<postID>
     */
    protected function runCreatePost()
    {
        $posttime = strtotime($this->request->getString('fld_postdate'));
        
        if (checkdate(date("m", $posttime), date("d", $posttime), date("Y", $posttime))) {
            $postdate = date("Y-m-d H:i:00", $posttime);
        }
        else {
            $postdate = date("Y-m-d H:i:00");
        }
        
        $newPost = [
            'title'           => $this->request->getString('fld_posttitle'),
            'content'         => $this->request->get('fld_postcontent'),
            'tags'            => $this->request->getString('fld_tags'),
            'teaser_image'    => $this->request->getString('fld_teaserimage'),
            'blog_id'         => $this->blog['id'],
            'draft'           => $this->request->getInt('fld_draft'),
            'allowcomments'   => $this->request->getInt('fld_allowcomment'),
            'type'            => $this->request->getString('fld_posttype'),
            'initialautosave' => 0,
            'timestamp'       => $postdate
        ];

        if (strlen($newPost['title']) == 0) {
            $this->response->redirect('/cms/posts/manage/' . $this->blog['id'], 'Please provide a title', 'error');
        }
        
        if ($newPost['type'] == 'video') {
            $newPost['videoid'] = $this->request->getString('fld_postvideoID');
            $newPost['videosource'] = $this->request->getString('fld_postvideosource');
        }
        
        if ($newPost['type'] == 'gallery') {
            $newPost['gallery_imagelist'] = $this->request->get('fld_gallery_imagelist');
        }

        if($postID = $this->request->getInt('fld_postid', false)) {
            // This should be the case as it should have been created when the autosave run
            if ($this->model->updatePost($postID, $newPost)) {
                $this->model->removeAutosave($postID);
            }
            else {
                $this->response->redirect('/cms/posts/edit/' . $postID, 'Error updating post', 'error');
            }
        }
        elseif (!$this->model->createPost($newPost)) {
            $this->response->redirect('/cms/posts/manage/' . $this->blog['id'], 'Error creating post', 'error');
        }

        BlogCMS::runHook('onPostCreated', ['post' => $newPost]);
        $this->response->redirect('/cms/posts/manage/' . $this->blog['id'], 'Post created', 'success');
    }
    
    /**
     * Handles POST /cms/posts/autosave
     */
    public function autosave()
    {
        $postID = $this->request->getInt('fld_postid');

        $data = [
            'title'         => $this->request->getString('fld_title'),
            'content'       => $this->request->getString('fld_content'),
            'tags'          => $this->request->getString('fld_tags'),
            'allowcomments' => $this->request->getInt('fld_allowcomments'),
            'type'          => $this->request->getString('fld_type'),
        ];

        $updateDB = $this->model->autosavePost($postID, $data);

        if($updateDB === false) {
            echo json_encode([
                'status' => 'failed',
                'message' => 'Could not run autosave - DB Update Error'
            ]);
        }
        elseif($updateDB > 0 && $updateDB !== $postID) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Post autosaved at ' . date('H:i'),
                'newpostid' => $updateDB
            ]);
        }
        else {
            echo json_encode([
                'status' => 'success',
                'message' => 'Post autosaved at ' . date('H:i')
            ]);
        }
    }

    /**
     * Handles POST /posts/cancelsave/<postID>
     * 
     * Cancel action from post screen
     */
    public function cancelsave()
    {
        // Delete autosave
        $this->model->removeAutosave($this->post['id']);

        if ($this->post['initialautosave'] == 1) {
            // Delete post
            $this->model->delete(['id' => $this->post['id']]);
        }

        $this->response->redirect('/cms/posts/manage/' . $this->blog['id']);
    }
    
    /**
     * Handles POST /posts/edit/<postID>
     * 
     * Edit an existing blog post
     */
    public function runEditPost()
    {
        // Check & Format date
        $posttime = strtotime($this->request->getString('fld_postdate'));
        
        if (checkdate(date("m", $posttime), date("d", $posttime), date("Y", $posttime))) {
            $postdate = date("Y-m-d H:i:00", $posttime);
        }
        else {
            $postdate = $arraypost['timestamp']; // Keep to original
        }
        
        $updates = [
            'title'           => $this->request->getString('fld_posttitle'),
            'content'         => $this->request->get('fld_postcontent'),
            'tags'            => $this->request->getString('fld_tags'),
            'teaser_image'    => $this->request->getString('fld_teaserimage'),
            'draft'           => $this->request->getInt('fld_draft'),
            'allowcomments'   => $this->request->getInt('fld_allowcomment'),
            'initialautosave' => 0,
            'timestamp'       => $postdate
        ];

        if (strlen($updates['title']) == 0) {
            $this->response->redirect('/cms/posts/manage/' . $this->blog['id'], 'Please provide a title', 'error');
        }
        
        if ($this->post['type'] == 'video') {
            $updates['videoid']     = $this->request->getString('fld_postvideoID');
            $updates['videosource'] = $this->request->getString('fld_postvideosource');
        }
        if ($this->post['type'] == 'gallery') {
            $updates['gallery_imagelist'] = $this->request->get('fld_gallery_imagelist');
        }
        
        $this->model->updatePost($this->post['id'], $updates);
        $this->model->removeAutosave($this->post['id']);
        
        BlogCMS::runHook('onPostUpdated', ['post' => array_merge($this->post, $updates)]);
        $this->response->redirect('/cms/posts/edit/' . $this->post['id'], 'Save successful', 'success');
    }
    
    /**
     * Handles /posts/delete/<postID>
     * 
     * @todo make sure there are no pages with this post ID
     */
    public function delete()
    {
        if($delete = $this->model->delete(['id' => $this->post['id']]) && $this->model->removeAutosave($this->post['id'])) {
            BlogCMS::runHook('onPostDeleted', ['post' => $this->post]);
            $this->response->redirect('/cms/posts/manage/' . $this->post['blog_id'], 'Blog post deleted', 'success');
        }
        else {
            $this->response->redirect('/cms/posts/manage/' . $this->post['blog_id'], 'Blog post deleted', 'error');
        }
    }
    
    /**
     * @todo exclude current post ID!!!
     */
    public function checkDuplicateTitle()
    {
        $blogID = $this->request->getInt('blog_id');
        $postID = $this->request->getInt('post_id', 0);
        $title = $this->request->getString('post_title', '');

        if (strlen($title) == 0) {
            print "true";
            return;
        } 

        $link = $this->model->createSafePostUrl($title);
        
        $matchingPosts = $this->model->count(['blog_id' => $blogID, 'link' => $link]);
        if ($matchingPosts == 0) {
            print "false";
            return;
        }
        elseif ($matchingPosts == 1) {
            $post = $this->model->getPostByURL($link, $blogID);
            // Valid if new post & only one match or if the found post
            // is the one we're editing
            if ($postID == 0 || $post['id'] == $postID) {
                print "false";
                return;
            }
        }

        print "true";
    }

}
