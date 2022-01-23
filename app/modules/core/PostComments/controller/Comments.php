<?php
namespace HamletCMS\PostComments\controller;

use HamletCMS\GenericController;
use HamletCMS\HamletCMS;

/**
 * @method all($request, $response)
 * @method deleteComment($commentID, $blog_id)
 * @method approveComment($commentID, $blog_id)
 * 
 * @author R Bertram <ricky@rbwebdesigns.co.uk>
 */
class Comments extends GenericController
{
    /** @var \HamletCMS\Blog\Blog Active blog */
    protected $blog = null;

    /** @var array Active comment */
    protected $comment = null;


    public function __construct()
    {
        $this->blog = HamletCMS::getActiveBlog();
        HamletCMS::$activeMenuLink = '/cms/comments/manage/'. $this->blog->id;
        parent::__construct();
    }

    /**
     * Check if the user has access to manage comments
     */
    protected function canAdminister($commentID)
    {
        if (!$comment = $this->model('comments')->getCommentById($commentID)) {
            return false;
        }
        HamletCMS::$blogID = $comment->blog_id;
        return $this->model('permissions')->userHasPermission('administer_comments');
    }

    /**
     * Handles /comments/manage/<blogid>
     * 
     * @todo Change this view to look more like the manage posts view with a seperate
     * ajax call to get the comments themselves?
     */
    public function manage()
    {
        $start = $this->request->getInt('page', 1);
        $perPage = 20; // hard coded for now - may expose later!
        $offset = ($start - 1) * $perPage;
        $limit = $offset . ',' . $perPage;
        
        $filter = $this->request->getString('filter', 'all');

        switch ($filter) {
            case 'approved':
                $comments = $this->model('comments')->getCommentsByBlog($this->blog->id, $limit, 1);
                $total = $this->model('comments')->countBlogComments($this->blog->id, 1);
            break;
            case 'pending':
                $comments = $this->model('comments')->getCommentsByBlog($this->blog->id, $limit, 0);
                $total = $this->model('comments')->countBlogComments($this->blog->id, 0);
            break;
            case 'all':
            default:
                $comments = $this->model('comments')->getCommentsByBlog($this->blog->id, $limit);
                $total = $this->model('comments')->countBlogComments($this->blog->id);
            break;
        }

        $pageCount = ceil($total / $perPage);

        $this->model('comments')->getCommentsByBlog($this->blog->id);

        $this->response->setVar('current_page', $start);
        $this->response->setVar('page_count', $pageCount);
        $this->response->setVar('comment_count', $total);

        $this->response->setVar('comments', $comments);
        $this->response->setVar('filter', $filter);

        $this->response->setVar('blog', $this->blog);
        $this->response->setTitle('Manage Comments - ' . $this->blog->name);
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
        elseif ($this->model('comments')->deleteComment($commentID)) {
            $this->response->routeRedirect('comments.manage', 'Comment removed', 'success');
        }
        else {
            $this->response->routeRedirect('comments.manage', 'Unable to remove comment', 'error');
        }
    }

    /**
     * Handles /comments/deleteunapproved/<blogid>
     */
    public function deleteUnapproved()
    {
        if ($this->model('permissions')->userHasPermission('administer_comments') && 
            $this->model('comments')->delete(['blog_id' => $this->blog->id, 'approved' => 0])) {
            $this->response->routeRedirect('comments.manage', 'Comments removed', 'success');
        }
        else {
            $this->response->routeRedirect('comments.manage', 'Unable to remove comments', 'error');
        }
    }
    
    /**
     * Handles /comments/approve/<commentid>
     */
    public function approve()
    {
        $commentID = $this->request->getUrlParameter(1);
        $comment = $this->model('comments')->getCommentById($commentID);

        if (!$this->canAdminister($commentID)) {
            $this->response->redirect('/cms', 'Unable to remove comment', 'error');
        }
        elseif ($this->model('comments')->approve($comment->id)) {
            $this->response->routeRedirect('comments.manage', 'Comment approved', 'success');
        }
        else {
            $this->response->routeRedirect('comments.manage', 'Unable to approve comment', 'error');
        }
    }

    /**
     * Handles /comments/approveall/<blogid>
     */
    public function approveAll()
    {
        if ($this->model('permissions')->userHasPermission('administer_comments') && 
            $this->model('comments')->approveAll($this->blog->id)) {
            $this->response->routeRedirect('comments.manage', 'Comments approved', 'success');
        }
        else {
            $this->response->routeRedirect('comments.manage', 'Unable to approve comments', 'error');
        }
    }

    /**
     * Add a comment to a blog post
     * 
     * @todo Check that the user hasn't submitted more than 5 comments in last 30 seconds?
     *   Or if the last X comments were from the same user? to prevent comment spamming
     */
    public function add()
    {
        $postID = $this->request->getInt('fld_postid', -1);
        $post = $this->model('posts')->getPostByID($postID);
        $blogID = $post->blog_id;
        $commentText = $this->request->getString('fld_comment');
        $currentUser = HamletCMS::session()->currentUser;

        // User not logged in
        if (!$currentUser) {
            $this->response->redirect("/blogs/{$blogID}", 'You must be logged in to add a comment', 'error');
        }

        // Couldn't find blog post
        if (!$post) {
            $this->response->redirect("/blogs/{$blogID}", 'Post not found', 'error');
        }
        
        // No comment text
        if (!isset($commentText) || strlen($commentText) == 0) {
            $this->response->redirect("/blogs/{$blogID}/posts/{$post->link}", 'Please enter a comment', 'error');
        }
        
        // Check that post allows reader comments
        if ($post->allowcomments == 0) {
            $this->response->redirect("/blogs/{$blogID}/posts/{$post->link}", 'Comments are not allowed here', 'error');
        }

        // Check that the user isn't comment spamming
        // Maximum 5 posts per minute - any post
        if ($this->model('comments')->count(['user_id' => $currentUser['id'], 'timestamp' => '>' . date('Y-m-d H:i:s', strtotime('-1 minute'))]) >= 5) {
            $this->response->redirect("/blogs/{$blogID}/posts/{$post->link}", 'Maximum 5 comments per minute', 'error');
        }

        // Success
        if ($this->model('comments')->addComment($commentText, $post->id, $blogID, $currentUser['id'])) {
            $this->response->redirect("/blogs/{$blogID}/posts/{$post->link}", 'Comment submitted - awaiting approval', 'success');
        }
        // Failed to save
        else {
            $this->response->redirect("/blogs/{$blogID}/posts/{$post->link}", 'Error adding comment', 'error');
        }
    }
    
    /**
     * Comment settings
     * 
     * Handles GET /cms/settings/comments/<blogid>
     */
    public function settings()
    {
        if ($this->request->method() == 'POST') return $this->saveSettings();

        HamletCMS::$activeMenuLink = '/cms/settings/menu/'. $this->blog->id;

        $config = $this->blog->config();
        $config = array_key_exists('comments', $config) ? $config['comments'] : [];
        
        $customTemplateFile  = SERVER_PATH_BLOGS .'/'. $this->blog->id .'/templates/comment.tpl';
        $defaultTemplateFile = SERVER_MODULES_PATH .'/core/PostComments/templates/defaultcomment.tpl';
        $templatePath = file_exists($customTemplateFile) ? $customTemplateFile : $defaultTemplateFile;
        $commentTemplate = file_get_contents($templatePath);

        $this->response->setVar('commentTemplate', $commentTemplate);
        $this->response->setVar('settings', $config);
        $this->response->addScript('/resources/ace/ace.js');
        $this->response->setTitle('Comment settings - ' . $this->blog->name);
        $this->response->setVar('blog', $this->blog);
        $this->response->write('settings.tpl', 'PostComments');
    }

    /**
     * Comment settings
     * 
     * Handles POST /cms/settings/comments/<blogid>
     */
    public function saveSettings()
    {
        // Save settings here
        $template = $this->request->get('comment_template');
        $update = file_put_contents(SERVER_PATH_BLOGS .'/'. $this->blog->id .'/templates/comment.tpl', $template);

        if ($update) {
            $this->response->routeRedirect('settings.comments', 'Settings saved', 'success');
        }
        else {
            $this->response->routeRedirect('settings.comments', 'Failed to save template', 'error');
        }
    }

}
