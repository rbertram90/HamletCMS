<?php
namespace rbwebdesigns\blogcms;

use rbwebdesigns\core\Sanitize;

/**
 * file /app/controller/comments_controller.inc.php
 * 
 * @method all($request, $response)
 * @method deleteComment($commentID, $blog_id)
 * @method approveComment($commentID, $blog_id)
 * 
 * @author R Bertram <ricky@rbwebdesigns.co.uk>
 */
class CommentsController extends GenericController
{
    /**
     * @var \rbwebdesigns\blogcms\model\Comments
     */
    protected $model;
    /**
     * @var \rbwebdesigns\blogcms\model\AccountFactory
     */
    protected $modelBlogs;
    /**
     * @var \rbwebdesigns\blogcms\model\Contributors
     */
    protected $modelContributors;


    public function __construct()
    {
        $this->model = BlogCMS::model('\rbwebdesigns\blogcms\model\Comments');
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\model\Blogs');
        $this->modelContributors = BlogCMS::model('\rbwebdesigns\blogcms\model\Contributors');

        BlogCMS::$activeMenuLink = 'comments';
    }

    /**
     * Handles /comments
     * Won't know which blog user is referring to in this case
     * so just send them back home with an error message
     */
    public function defaultAction(&$request, &$response)
    {
        return $response->redirect('/', 'Invalid request', 'error');
    }

    /**
     * Administer comments made on the blog
     * Handles /comments/all/<blogid>
     * 
     * @todo Change this view to look more like the manage posts view with a seperate
     * ajax call to get the comments themselves?
     */
    public function all(&$request, &$response)
    {
        // Get the Blog ID
        $blogID = $request->getUrlParameter(1);
        
        // View Current Comments
        $comments = $this->model->getCommentsByBlog($blogID);
        $blog = $this->modelBlogs->getBlogById($blogID);

        $response->setVar('comments', $comments);
        $response->setVar('blog', $blog);
        $response->setTitle('Manage Comments - ' . $blog['name']);
        $response->addScript('/resources/js/paginate.js');
        $response->write('comments.tpl');
    }
    
    /**
     * deleteComment
     * remove a comment from a blog
     */
    public function delete(&$request, &$response)
    {
        $commentID = $request->getUrlParameter(1);
        $currentUser = BlogCMS::session()->currentUser;

        if (!$comment = $this->model->getCommentById($commentID)) {
            $response->redirect('/', 'Comment not found', 'error');
        }

        $blogID = $comment['blog_id'];
        if (!$blog = $this->modelBlogs->getBlogById($blogID)) {
            $response->redirect('/', 'Blog not found', 'error');
        }

        if (!$this->modelContributors->isBlogContributor($blogID, $currentUser['id'])) {
            $response->redirect('/', 'Access denied', 'error');
        }

        if($this->model->deleteComment($commentID)) {
            $response->redirect('/comments/all/' . $blog['id'], 'Comment removed', 'success');
        }
        else {
            $response->redirect('/comments/all/' . $blog['id'], 'Unable to remove comment', 'error');
        }
    }
    
    /**
     * approveComment
     * @description set approved=1 for a comment
     * 
     * @todo refactor to stop repeat code
     */
    public function approve(&$request, &$response)
    {
        /* Duplicate code! */
        $commentID = $request->getUrlParameter(1);
        $currentUser = BlogCMS::session()->currentUser;

        if (!$comment = $this->model->getCommentById($commentID)) {
            $response->redirect('/', 'Comment not found', 'error');
        }

        $blogID = $comment['blog_id'];
        if (!$blog = $this->modelBlogs->getBlogById($blogID)) {
            $response->redirect('/', 'Blog not found', 'error');
        }

        if (!$this->modelContributors->isBlogContributor($blogID, $currentUser['id'])) {
            $response->redirect('/', 'Access denied', 'error');
        }
        /* End Duplicate code! */
        
        if($this->model->approve($commentID)) {
            $response->redirect('/comments/all/' . $blog['id'], 'Comment approved', 'success');
        }
        else {
            $response->redirect('/comments/all/' . $blog['id'], 'Unable to approve comment', 'error');
        }
    }
    
}