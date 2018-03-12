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
     * manageComments
     * Handles /comments/all/<blogid>
     * view all comments that have been made on the blog and give the option to delete
     * 
     * @param <array> split query string
     * @todo Change this view to look more like the manage posts view with a seperate
     * ajax call to get the comments themselves
     */
    public function all(&$request, &$response)
    {
        // Get the Blog ID
        $blogID = $request->getUrlParameter(1);
        $currentUser = BlogCMS::session()->currentUser;
        
        // Check user has permissions to view comments
        if (!$this->modelContributors->isBlogContributor($blogID, $currentUser['id'])) {
            return $this->throwAccessDenied();
        }
        
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