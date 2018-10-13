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
     * @var \rbwebdesigns\blogcms\model\Comments
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

        BlogCMS::$activeMenuLink = 'comments';

        $this->request = BlogCMS::request();
        $this->response = BlogCMS::response();
        $this->blog = BlogCMS::getActiveBlog();
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