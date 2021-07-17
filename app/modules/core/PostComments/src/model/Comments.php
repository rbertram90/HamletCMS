<?php
namespace HamletCMS\PostComments\model;

use rbwebdesigns\core\model\RBFactory;
use rbwebdesigns\core\Sanitize;

/**
 * /app/model/mdl_comment.inc.php
 * Access to the comment database is done through this class
 * 
 * @author R Bertram <ricky@rbwebdesigns.co.uk>
 */
class Comments extends RBFactory
{

    /**
     * @param \rbwebdesigns\core\model\ModelManager $modelFactory
     */
    function __construct($modelFactory)
    {
        $this->db = $modelFactory->getDatabaseConnection();
        $this->tableName = 'comments';
        $this->subClass = '\\HamletCMS\\PostComments\\Comment';

        $this->fields = array(
            'id' => 'number',
            'message' => 'memo',
            'blog_id' => 'number',
            'post_id' => 'number',
            'timestamp' => 'datetime',
            'user_id' => 'number',
            'approved' => 'boolean'
        );
    }
    
    /**
     * Get a comment by ID
     * 
     * @param int $commentID
     * 
     * @return \HamletCMS\PostCommments\Comment
     */
    public function getCommentById($commentID)
    {
        return $this->get('*', ['id' => $commentID], null, null, false);
    }
    
    /**
     * Get all comments made on a blog
     * 
     * @param int $blogID
     * @param bool $limit
     * 
     * @return \HamletCMS\PostCommments\Comment[]
     */
    public function getCommentsByBlog($blogID, $limit=null, $approved=null)
    {
        $where = ['blog_id' => $blogID];

        if (!is_null($approved)) {
            $where['approved'] = $approved;
        }

        return $this->get('*', $where, null, $limit);
    }
    
    /**
     * Get all comments made on a post
     * 
     * @param int $postID
     * @param bool $includeApprovals
     *   Include comments awaiting approval?
     * 
     * @return \HamletCMS\PostCommments\Comment[]
     */
    public function getCommentsByPost($postID, $includeApprovals=true)
    {
        $where = ['post_id' => $postID];
        if (!$includeApprovals) $where['approved'] = 1;
        return $this->get('*', $where);
    }

    /**
     * Get all comments made by a user
     * 
     * @param int $userID
     * @param bool $includeApprovals
     *   Include comments awaiting approval?
     * 
     * @return \HamletCMS\PostCommments\Comment[]
     */
    public function getCommentsByUser($userID, $includeApprovals=true)
    {
        $where = ['user_id' => $userID];
        if (!$includeApprovals) $where['approved'] = 1;
        return $this->get('*', $where);
    }

    /**
     * Count the number of comments for a blog
     * 
     * @param int $blogID
     * 
     * @return int record count
     */
    function countBlogComments($blogID, $approved=null)
    {
        $where = ['blog_id' => $blogID];

        if (!is_null($approved)) {
            $where['approved'] = $approved;
        }

        return $this->db->countRows($this->tableName, $where);
    }
    
    /**
     * Count the number of comments for a blog post
     * 
     * @param int $postID
     * 
     * @return int record count
     */
    function countPostComments($postID)
    {
        return $this->db->countRows($this->tableName, ['post_id' => $postID]);
    }
        
    /**
     * Create a new comment
     * 
     * @param string $comment
     * @param int $postID
     * @param int $blogID
     * @param int $userID
     * 
     * @return boolean was the insert successful?
     */
    public function addComment($comment, $postID, $blogID, $userID)
    {
        return $this->insert([
            'message' => $comment,
            'blog_id' => $blogID,
            'post_id' => $postID,
            'timestamp' => date("Y-m-d H:i:s"),
            'user_id' => $userID,
        ]);
    }
    
    /**
     * Delete a comment
     * 
     * @param int $commentID
     * 
     * @return bool was the delete successful?
     */
    public function deleteComment($commentID)
    {
        return $this->delete(['id' => $commentID]);
    }
    
    /**
     * Approve a comment
     * 
     * @param int $commentID
     * 
     * @return bool was the approval successful?
     */
    public function approve($commentID)
    {
        return $this->update(['id' => $commentID], ['approved' => 1]);
    }

    /**
     * Approve all comments for a blog
     * 
     * @param int $blogID
     * 
     * @return bool was the approval successful?
     */
    public function approveAll($blogID)
    {
        return $this->update(['blog_id' => $blogID], ['approved' => 1]);
    }

}
