<?php
namespace rbwebdesigns\blogcms;
use rbwebdesigns\core\Sanitize;
use rbwebdesigns\core\AppSecurity;
use Codeliner\ArrayReader\ArrayReader;

/**********************************************************************
class PostsController

This is the controller which acts as the intermediatory between the
model (database) and the view. Any requests to the model are sent from
here rather than the view.

Example requests that will be handled here:
    /posts/1298340239
    /posts/1298340239/new
    /posts/1298340239/new/submit
    /posts/1298340239/edit/67
    /posts/1298340239/edit/67/submit
    /posts/1298340239/delete/67
    
**********************************************************************/

class PostsController extends GenericController
{
    /**
     * @var \rbwebdesigns\blogcms\model\Posts
     */
    protected $model;
    /**
     * @var \rbwebdesigns\blogcms\model\Blogs
     */
    protected $modelBlogs;
    /**
     * @var \rbwebdesigns\blogcms\model\Contributors
     */
    protected $modelContributors;

    public function __construct()
    {
        $this->model = BlogCMS::model('\rbwebdesigns\blogcms\model\Posts');
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\model\Blogs');
        $this->modelContributors = BlogCMS::model('\rbwebdesigns\blogcms\model\Contributors');
    }
    
    /**
     * function: route
     * 
     * @param array $params
     *   Miscellaneous inputs from the URL such as blog id accessed in an array.
     */
    public function route($params)
    {
        // Create an easy reader for the params array
        $paramsReader = new ArrayReader($params);
        $currentUser = BlogCMS::session()->currentUser;

        if(getType($params) == 'array' && array_key_exists(0, $params))
        {
            // Find and open blog
            $blogid = Sanitize::int($params[0]);
            $arrayblog = $this->modelBlogs->getBlogById($blogid);
            
            // Check we found a blog
            if(getType($arrayblog) != 'array') return $this->throwNotFound();
            
            // Check that user can at least manage posts on this blog - this will be all contributors
            if(!$this->modelContributors->isBlogContributor($arrayblog['id'], $currentUser)) return $this->throwAccessDenied();
            
            // Find and open post (if needed)
            //$postid = $paramsReader->integerValue(2, false);
            $postid = array_key_exists(2, $params) && is_numeric($params[2]) ? Sanitize::int($params[2]) : false;
            
            if($postid !== false)
            {
                $arraypost = $this->modelPosts->getPostById($postid);
                
                if(getType($arraypost) != 'array') $this->throwNotFound();
                
                // Convert gallery image list to array
                if($arraypost['type'] == 'gallery')
                {
                    $arraypost['gallery_imagelist'] = explode(',', $arraypost['gallery_imagelist']);
                }
            }
            
            $action = array_key_exists(1, $params) ? Sanitize::string($params[1]) : ''; // Action
            $formsubmitted = (array_key_exists(2, $params) && $params[2] == 'submit'); // Submit
            if (!$formsubmitted) $formsubmitted = (array_key_exists(3, $params) && $params[3] == 'submit'); // Submit
            
            switch($action)
            {                                    
                case 'delete':
                    $this->action_deletePost($arraypost);
                    break;
        
                case 'preview':
                    $this->previewPost();
                    break;
        
                case 'cancelsave':
                    $this->action_removeAutosave($arraypost);
                    break;
            }
        }
    }
        
    /**
     * View post overview page
     * Handles /posts/manage/<blogid>
     * Most of the heavy data is done in a seperate ajax call
     */
    public function manage(&$request, &$response)
    {
        $blogID = $request->getUrlParameter(1);
        $blog = $this->modelBlogs->getBlogById($blogID);

        $response->setVar('blog', $blog);
        $response->setTitle('Manage Posts - ' . $blog['name']);
        $response->addScript('/js/showUserCard.js');
        $response->write('posts/manage.tpl');
    }
    
    /**
     * View new post form
     */
    public function create(&$request, &$response)
    {
        $blogID = $request->getUrlParameter(1);
        $blog = $this->modelBlogs->getBlogById($blogID);

        if ($request->method() == 'POST') return $this->runCreatePost($request, $response, $blog);

        $response->setVar('blog', $blog);
        $response->setTitle('New Post');
        
        $response->addScript('/resources/js/rbwindow.css');
        $response->addScript('/resources/js/rbrtf.css');
        $response->addStylesheet('/resources/css/rbwindow.css');
        $response->addStylesheet('/resources/css/rbrtf.css');
        
        $viewtype = $request->getUrlParameter(2);

        switch($viewtype) {
            case 'video':
            $response->write('posts/videopost.tpl');
            break;
            
            case 'gallery':
            $response->write('posts/gallerypost.tpl');
            break;
            
            case 'standard':
            $response->write('posts/standardpost.tpl');
            break;
            
            default:
            $response->write('posts/newpostmenu.tpl');
            break;
        }
    }
    
    /**
     * View edit post form
     */
    public function edit(&$request, &$response)
    {
        $postid = $request->getUrlParameter(1);
        $post = $this->model->getPostById($postid);

        $blogid = $post['blog_id'];
        $blog = $this->modelBlogs->getBlogById($blogid);

        if ($request->method() == 'POST') {
            return $this->runEditPost($request, $response, $post);
        }

        if (getType($blog) != 'array' || getType($post) != 'array') {
            $response->redirect('/', 'Unable to load content', 'error');
        }
        
        if ($post['type'] == 'gallery') {
            $post['gallery_imagelist'] = explode(',', $post['gallery_imagelist']);
        }

        // Check permission
        $currentUser = BlogCMS::session()->currentUser;
        if (!($this->modelContributors->isBlogContributor($post['blog_id'], $currentUser['id'], 'all') || $currentUser['id'] == $post['author_id'])) {
            $response->redirect('/', 'Permission denied', 'error');
        }

        if ($post['initialautosave'] == 1) {
            // get the most recent content from the actual autosave record
            $autosave = $this->model->getAutosave($post['id']);

            if (getType($autosave) == 'array') {
                $post = array_merge($post, $autosave);
            }

            $this->model->update(['id' => $post['id']], ['initialautosave' => 0]);
        }
        elseif ($this->model->autosaveExists($post['id'])) {
            // Has been edited without being saved
            $post['autosave'] = $this->model->getAutosave($post['id']);
        }
                
        $response->setVar('post', $post);
        $response->setVar('blog', $blog);
        $response->setTitle('Edit Post - ' . $post['title']);
        
        $response->addScript('/resources/js/rbwindow.js');
        $response->addScript('/resources/js/rbrtf.js');
        $response->addStylesheet('/resources/css/rbwindow.css');
        $response->addStylesheet('/resources/css/rbrtf.css');

        switch($post['type']) {
            case 'video':
            $response->write('posts/videopost.tpl');
            break;
            
            case 'gallery':
            $response->write('posts/gallerypost.tpl');
            break;
            
            default:
            $response->write('posts/standardpost.tpl');
            break;
        }
    }
    
    /**
     * @todo Everything!
     */
    public function previewPost()
    {
        echo "Preview Post - functionality to be completed!";
        exit;
    }
    

    /******************************************************************
        POST Requests (Form Submitted)
    ******************************************************************/
    
    /**
     * Create a new blog post
     */
    protected function runCreatePost(&$request, &$response, $blog)
    {
        $posttime = strtotime($request->getString('fld_postdate'));
        
        if (checkdate(date("m", $posttime), date("d", $posttime), date("Y", $posttime))) {
            $postdate = date("Y-m-d H:i:00", $posttime);
        }
        else {
            $postdate = date("Y-m-d H:i:00");
        }
        
        $newPost = [
            'title'           => $request->getString('fld_posttitle'),
            'content'         => $request->getString('fld_postcontent'),
            'tags'            => $request->getString('fld_tags'),
            'blog_id'         => $blog['id'],
            'draft'           => $request->getInt('fld_draft'),
            'allowcomments'   => $request->getInt('fld_allowcomment'),
            'type'            => $request->getString('fld_posttype'),
            'initialautosave' => 0,
            'timestamp'       => $postdate
        ];
        
        if ($newPost['type'] == 'video') {
            $newPost['videoid'] = $request->getString('fld_postvideoID');
            $newPost['videosource'] = $request->getString('fld_postvideosource');
        }
        
        if ($newPost['type'] == 'gallery') {
            $newPost['gallery_imagelist'] = $request->get('fld_gallery_imagelist');
        }

        if($postID = $request->getInt('fld_postid', false)) {
            // This should be the case as it should have been created when the autosave run
            if ($this->model->updatePost($postID, $newPost)) {
                $this->model->removeAutosave($postID);
            }
            else {
                $response->redirect('/posts/edit/' . $postID, 'Error updating post', 'error');
            }
        }
        elseif (!$this->model->createPost($newPost)) {
            $response->redirect('/posts/manage/' . $blog['id'], 'Error creating post', 'error');
        }

        $response->redirect('/posts/manage/' . $blog['id'], 'Post created', 'success');
    }
    
    // Cancel action from new post screen
    protected function action_removeAutosave($post)
    {
        $currentUser = BlogCMS::session()->currentUser;

        // Check we have permission to perform action - if the user created the post or is blog admin
        if(!($this->modelContributors->isBlogContributor($post['blog_id'], $currentUser, 'all') || $currentUser == $post['author_id'])) return $this->throwAccessDenied();
        
        // Delete from DB - isn't critical if fails
        $this->modelPosts->removeAutosave($post['id']);
        
        // Check if the actual post record is an autosave
        if($post['initialautosave'] == 1)
        {
            $this->modelPosts->delete(array('id' => $post['id']));
        }
        
        // Redirect back to manage posts
        redirect('/posts/' . $post['blog_id']);
    }
    
    /**
     *  Edit an existing blog post
    **/
    public function runEditPost($request, $response, $post)
    {
        $currentUser = BlogCMS::session()->currentUser;

        // Re-check security with heightened permissions
        if (!($this->modelContributors->isBlogContributor($post['blog_id'], $currentUser['id'], 'all') || $currentUser['id'] == $post['author_id'])) {
            $response->redirect('/posts/manage/' . $post['blog_id'], 'You do not have permission to do that', 'error');
        }
        
        // Check & Format date
        $posttime = strtotime($request->getString('fld_postdate'));
        
        if (checkdate(date("m", $posttime), date("d", $posttime), date("Y", $posttime))) {
            $postdate = date("Y-m-d H:i:00", $posttime);
        }
        else {
            $postdate = $arraypost['timestamp']; // Keep to original
        }
        
        $updates = [
            'title'           => $request->getString('fld_posttitle'),
            'content'         => $request->getString('fld_postcontent'),
            'tags'            => $request->getString('fld_tags'),
            'draft'           => $request->getInt('fld_draft'),
            'allowcomments'   => $request->getInt('fld_allowcomment'),
            'initialautosave' => 0,
            'timestamp'       => $postdate
        ];
        
        if ($post['type'] == 'video') {
            $updates['videoid']     = $request->getString('fld_postvideoID');
            $updates['videosource'] = $request->getString('fld_postvideosource');
        }
        if ($post['type'] == 'gallery') {
            $updates['gallery_imagelist'] = $request->get('fld_gallery_imagelist');
        }
        
        $this->model->updatePost($post['id'], $updates);
        $this->model->removeAutosave($post['id']);
        
        $response->redirect('/posts/edit/' . $post['id'], 'Save successful', 'success');
    }
    
    /**
     * Handles /posts/delete/<postID>
     * 
     * @todo make sure there are no pages with this post ID
     */
    public function delete(&$request, &$response)
    {
        $currentUser = BlogCMS::session()->currentUser;
        $postID = $request->getUrlParameter(1);

        if(!$post = $this->model->getPostById($postID)) {
            $response->redirect('/', 'Could not find blog post', 'error');
        }

        // Check we have permission to perform action - if the user created the post or is blog admin
        if(!($this->modelContributors->isBlogContributor($post['blog_id'], $currentUser['id'], 'all') || $currentUser['id'] == $post['author_id'])) {
            $response->redirect('/', 'You do not have access to delete this blog post', 'error');
        }
        
        if($delete = $this->model->delete(['id' => $post['id']]) && $this->model->removeAutosave($post['id'])) {
            $response->redirect('/', 'Blog post deleted', 'success');
        }
        else {
            $response->redirect('/posts/manage/' . $post['blog_id'], 'Blog post deleted', 'error');
        }
    }
    
}
