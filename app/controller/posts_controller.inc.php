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
                case 'new':
                    if($formsubmitted) $this->action_createPost();
                    $posttype = $paramsReader->stringValue(2, '');        
                    $this->createPost($arrayblog, $posttype);
                    break;
                    
                case 'edit':
                    if($formsubmitted) $this->action_editPost($arraypost);
                    $this->editPost($arrayblog, $arraypost);
                    break;
                
                case 'delete':
                    $this->action_deletePost($arraypost);
                    break;
        
                case 'preview':
                    $this->previewPost();
                    break;
        
                case 'cancelsave':
                    $this->action_removeAutosave($arraypost);
                    break;
                
                default:
                    $this->managePosts($arrayblog);
                    break;
            }
        }
        else
        {
            // Must have at least one param -> blogid
            return $this->throwNotFound();
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
        View new post form
    **/
    public function create(&$request, &$response)
    {
        $blogID = $request->getUrlParameter(1);
        $blog = $this->modelBlogs->getBlogById($blogID);

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
        View edit post form
    **/
    public function editPost($arrayblog, $arraypost)
    {
        $currentUser = BlogCMS::session()->currentUser;

        if($arraypost['initialautosave'] == 1)
        {
            // get the most recent content from the actual autosave record
            $arrayAutosave = $this->modelPosts->getAutosave($arraypost['id']);

            if(getType($arrayAutosave) == 'array')
            {
                // Merge the two
                $arraypost = array_merge($arraypost, $arrayAutosave);
            }

            // mark as autosave off
            $this->modelPosts->update(array('id' => $arraypost['id']), array('initialautosave' => 0));
            
        }
        elseif($this->modelPosts->autosaveExists($arraypost['id']))
        {
            // Has been edited without being saved
            // Show a notice
            $arraypost['autosave'] = $this->modelPosts->getAutosave($arraypost['id']);
        }
        
        // Check we have permission to perform action
        if(!($this->modelContributors->isBlogContributor($arraypost['blog_id'], $currentUser, 'all') || $currentUser == $arraypost['author_id'])) return $this->throwAccessDenied();
                
        $this->view->setVar('post', $arraypost);
        $this->view->setVar('blog', $arrayblog);
        $this->view->setPageTitle('Edit Post - '.$arraypost['title']);
        
        $this->view->addScript('/resources/js/rbwindow');
        $this->view->addScript('/resources/js/rbrtf');
        $this->view->addStylesheet('/resources/css/rbwindow');
        $this->view->addStylesheet('/resources/css/rbrtf');

        switch($arraypost['type'])
        {
            case 'video':
            $this->view->render('posts/videopost.tpl');
            break;
            
            case 'gallery':
            $this->view->render('posts/gallerypost.tpl');
            break;
            
            default:
            case 'standard':
            $this->view->render('posts/standardpost.tpl');
            break;
        }
    }
    
    public function previewPost() {
        
        echo "Preview Post - functionality to be completed!";
        
        exit; // go no further!
    }
    

    /******************************************************************
        POST Requests (Form Submitted)
    ******************************************************************/
    
    /**
        Create a new blog post
    **/
    public function action_createPost()
    {
        // Check & Format date
        $posttime = strtotime($_POST['fld_postdate']);
        
        if(checkdate(date("m", $posttime), date("d", $posttime), date("Y", $posttime)))
        {
            $postdate = date("Y-m-d H:i:00", $posttime);
        }
        else
        {
            $postdate = date("Y-m-d H:i:00"); // Default to now
        }
        
        $newPost = array(
            'title'           => $_POST['fld_posttitle'],
            'content'         => $_POST['fld_postcontent'],
            'tags'            => $_POST['fld_tags'],
            'blog_id'         => $_POST['fld_blogid'],
            'draft'           => $_POST['fld_draft'],
            'allowcomments'   => $_POST['fld_allowcomment'],
            'type'            => $_POST['fld_posttype'],
            'initialautosave' => 0,
            'timestamp'       => $postdate
        );
        
        // Add additional fields for video post
        if($newPost['type'] == 'video')
        {
            $newPost['videoid'] = $_POST['fld_postvideoID'];
            $newPost['videosource'] = $_POST['fld_postvideosource'];
        }
        
        // Add additional fields for gallery post
        if($newPost['type'] == 'gallery')
        {
            $newPost['gallery_imagelist'] = $_POST['fld_gallery_imagelist'];
        }
        
        // Add to database
        if(array_key_exists('fld_postid', $_POST))
        {
            // This should be the case as it should have been created when the autosave run
            $postid = Sanitize::int($_POST['fld_postid']);
            
            if($this->modelPosts->updatePost($postid, $newPost))
            {
                // Remove any autosaves
                $this->modelPosts->removeAutosave($postid);

                setSystemMessage(ITEM_CREATED, 'Success');
            }
            else setSystemMessage('Failed when saving post', 'Error');
        }
        else
        {
            if($this->modelPosts->createPost($newPost)) setSystemMessage(ITEM_CREATED, 'Success');
            else setSystemMessage('Error Creating Post', 'Error');
        }
        
        redirect('/posts/'.$_POST['fld_blogid']);
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
        Edit an existing blog post
    **/
    public function action_editPost($arraypost)
    {
        $currentUser = BlogCMS::session()->currentUser;

        // Re-check security with heightened permissions
        if(!($this->modelContributors->isBlogContributor($_POST['fld_blogid'], $currentUser, 'all') || $currentUser == $arraypost['author_id'])) return $this->throwAccessDenied();
        
        // Check & Format date
        $posttime = strtotime($_POST['fld_postdate']);
        
        if(checkdate(date("m", $posttime), date("d", $posttime), date("Y", $posttime)))
        {
            $postdate = date("Y-m-d H:i:00", $posttime);
        }
        else
        {
            $postdate = $arraypost['timestamp']; // Keep to original
        }
        
        $arrayPostUpdates = array(
            'title' => $_POST['fld_posttitle'],
            'content' => $_POST['fld_postcontent'],
            'tags' => $_POST['fld_tags'],
            'draft' => $_POST['fld_draft'],
            'allowcomments' => $_POST['fld_allowcomment'],
            'initialautosave' => 0,
            'timestamp' => $postdate
        );
        
        // Add additional fields for video post
        if($arraypost['type'] == 'video')
        {
            $arrayPostUpdates['videoid'] = $_POST['fld_postvideoID'];
            $arrayPostUpdates['videosource'] = $_POST['fld_postvideosource'];
        }
        
        if($arraypost['type'] == 'gallery')
        {
            $arrayPostUpdates['gallery_imagelist'] = $_POST['fld_gallery_imagelist'];
        }
        
        // Update DB with post information
        $this->modelPosts->updatePost($_POST['fld_postid'], $arrayPostUpdates);
        
        // Remove Autosave
        $this->modelPosts->removeAutosave($_POST['fld_postid']);
        
        // Set Success Message
        setSystemMessage(ITEM_UPDATED, 'Success');
        redirect('/posts/'.$_POST['fld_blogid'].'/edit/'.$_POST['fld_postid']);
    }
    
    
    /**
        Delete a blog post
    **/
    public function action_deletePost($arraypost) {
        $currentUser = BlogCMS::session()->currentUser;

        // Check we have permission to perform action - if the user created the post or is blog admin
        if(!($this->modelContributors->isBlogContributor($arraypost['blog_id'], $currentUser, 'all') || $currentUser == $arraypost['author_id'])) return $this->throwAccessDenied();
        
        // Perform Database query (using generic delete)
        $delete = $this->modelPosts->delete(array('id' => $arraypost['id']));
        
        // Check outcome
        if($delete) setSystemMessage(ITEM_DELETED, 'Success');
        else setSystemMessage('Sorry, There has been an error deleting your post.', 'Error');
        
        // Redirect
        redirect('/posts/' . $arraypost['blog_id']);
    }
    
}
?>