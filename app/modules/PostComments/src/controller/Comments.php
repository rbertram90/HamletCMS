<?php
namespace rbwebdesigns\blogcms\PostComments\controller;

use rbwebdesigns\blogcms\Contributors\model\ContributorGroups;
use rbwebdesigns\core\Sanitize;
use rbwebdesigns\blogcms\GenericController;
use rbwebdesigns\blogcms\BlogCMS;

/**
 * @method all($request, $response)
 * @method deleteComment($commentID, $blog_id)
 * @method approveComment($commentID, $blog_id)
 * 
 * @author R Bertram <ricky@rbwebdesigns.co.uk>
 */
class Comments extends GenericController
{
    /**
     * @var \rbwebdesigns\blogcms\PostComments\model\Comments
     */
    protected $model;
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
        $this->model = BlogCMS::model('\rbwebdesigns\blogcms\PostComments\model\Comments');
        $this->modelPermissions = BlogCMS::model('\rbwebdesigns\blogcms\Contributors\model\Permissions');
        $this->blog = BlogCMS::getActiveBlog();

        BlogCMS::$activeMenuLink = '/cms/comments/all/'. $this->blog['id'];

        parent::__construct();
    }

    /**
     * Check if the user has access to manage comments
     */
    protected function canAdminister($commentID)
    {
        if (!$comment = $this->model->getCommentById($commentID)) {
            return false;
        }
        BlogCMS::$blogID = $comment['blog_id'];
        return $this->modelPermissions->userHasPermission('administer_comments');
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
        $this->response->write('comments.tpl', 'PostComments');
    }
    
    /**
     * Handles /comments/delete/<commentid>
     */
    public function delete()
    {
        $commentID = $this->request->getUrlParameter(1);

        if (!$this->canAdminister($commentID)) {
            $this->response->redirect('/cms', 'Unable to remove comment', 'error');
        }
        elseif ($this->model->deleteComment($commentID)) {
            $blog = BlogCMS::getActiveBlog();
            $this->response->redirect('/cms/comments/all/' . $blog['id'], 'Comment removed', 'success');
        }
        else {
            $blog = BlogCMS::getActiveBlog();
            $this->response->redirect('/cms/comments/all/' . $blog['id'], 'Unable to remove comment', 'error');
        }
    }
    
    /**
     * Handles /comments/approve/<commentid>
     */
    public function approve()
    {
        $commentID = $this->request->getUrlParameter(1);

        if (!$this->canAdminister($commentID)) {
            $this->response->redirect('/cms', 'Unable to remove comment', 'error');
        }
        elseif ($this->model->approve($this->comment['id'])) {
            $blog = BlogCMS::getActiveBlog();
            $this->response->redirect('/cms/comments/all/' . $blog['id'], 'Comment approved', 'success');
        }
        else {
            $blog = BlogCMS::getActiveBlog();
            $this->response->redirect('/cms/comments/all/' . $blog['id'], 'Unable to approve comment', 'error');
        }
    }
    
}