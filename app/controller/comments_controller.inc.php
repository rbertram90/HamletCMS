<?php
namespace rbwebdesigns\blogcms;

use rbwebdesigns\blogcms\model\ContributorGroups;
use rbwebdesigns\core\Sanitize;

/**
 * file /app/controller/comments_controller.inc.php
 * 
 * Routes:
 *   /comments/all/<blogid>
 *   /comments/approve/<commentid>
 *   /comments/delete/<commentid>
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
     * @var array Active comment
     */
    protected $comment = null;


    public function __construct()
    {
        $this->model = BlogCMS::model('\rbwebdesigns\blogcms\model\Comments');
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\model\Blogs');
        $this->modelContributors = BlogCMS::model('\rbwebdesigns\blogcms\model\Contributors');

        BlogCMS::$activeMenuLink = 'comments';

        $this->request = BlogCMS::request();
        $this->response = BlogCMS::response();

        $this->setup();
    }

    /**
     * Setup controller
     * 
     * 1. Gets the key records that will be used for any request to keep
     *    the code DRY (Blog and Comment)
     * 
     * 2. Checks the user has permissions to run the request
     */
    protected function setup()
    {
        $currentUser = BlogCMS::session()->currentUser;

        if (!BlogCMS::$blogID) {
            $commentID = $this->request->getUrlParameter(1);

            if (!$this->comment = $this->model->getCommentById($commentID)) {
                $this->response->redirect('/cms', 'Unable to find comment', 'error');
            }

            BlogCMS::$blogID = $this->comment['blog_id'];
        }

        $this->blog = BlogCMS::getActiveBlog();

        $access = true;

        // Check the user is a contributor of the blog to begin with
        if (!$this->modelContributors->isBlogContributor($currentUser['id'], $this->blog['id'])) {
            $access = false;
        }
        elseif (!$this->modelContributors->userHasPermission($currentUser['id'], $this->blog['id'], ContributorGroups::GROUP_MANAGE_COMMENTS)) {
            $access = false;
        }

        if (!$access) {
            $this->response->redirect('/cms', '403 Access Denied', 'error');
        }
    }

    /**
     * Handles /comments
     * Won't know which blog user is referring to in this case
     * so just send them back home with an error message
     */
    public function defaultAction()
    {
        return $this->response->redirect('/cms', 'Invalid request', 'error');
    }

    /**
     * Handles /comments/all/<blogid>
     * 
     * @todo Change this view to look more like the manage posts view with a seperate
     * ajax call to get the comments themselves?
     */
    public function all()
    {
        $this->response->setVar('comments', $this->model->getCommentsByBlog($this->blog['id']));
        $this->response->setVar('blog', $this->blog);
        $this->response->setTitle('Manage Comments - ' . $this->blog['name']);
        $this->response->addScript('/resources/js/paginate.js');
        $this->response->write('comments.tpl');
    }
    
    /**
     * Handles /comments/delete/<commentid>
     */
    public function delete()
    {
        if ($this->model->deleteComment($this->comment['id'])) {
            $this->response->redirect('/cms/comments/all/' . $this->blog['id'], 'Comment removed', 'success');
        }
        else {
            $this->response->redirect('/cms/comments/all/' . $this->blog['id'], 'Unable to remove comment', 'error');
        }
    }
    
    /**
     * Handles /comments/approve/<commentid>
     */
    public function approve()
    {
        if ($this->model->approve($this->comment['id'])) {
            $this->response->redirect('/cms/comments/all/' . $this->blog['id'], 'Comment approved', 'success');
        }
        else {
            $this->response->redirect('/cms/comments/all/' . $this->blog['id'], 'Unable to approve comment', 'error');
        }
    }
    
}