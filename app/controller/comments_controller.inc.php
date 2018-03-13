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
     * 
     * @param $commentID <int> Unique ID for the comment
     * @param $blog_id <int> Unique ID for the blog
     */
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
     * approveComment
     * @description set approved=1 for a comment
     * @param $commentID <int> Unique ID for the comment
     * @param $blog_id <int> Unique ID for the blog
     */
    protected function approveComment($commentID, $blog_id)
    {
        // Update database
        $this->modelComments->approve($commentID);
        
        // Set the message to show on the next page
        setSystemMessage('Comment approved', 'Success');
        
        // Redirect back to comments page
        redirect('/comments/' . $blog_id);
    }
    
}