<?php
namespace rbwebdesigns\blogcms\PostComments\model;

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
    protected $db, $tableName;

    /**
     * @param \rbwebdesigns\core\model\ModelManager $modelFactory
     */
    function __construct($modelFactory)
    {
        $this->db = $modelFactory->getDatabaseConnection();
        $this->tableName = 'comments';
        $this->subClass = '\\rbwebdesigns\\blogcms\\PostComments\\Comment';

        $this->fields = array(
            'id' => 'number',
            'message' => 'memo',
            'blog_id' => 'number',
            'post_id' => 'number',
            'timestamp' => 'datetime',
            'user_id' => 'number'
        );
    }
    
    /**
     * Get a comment by ID
     * 
     * @param int $commentID
     * 
     * @return \rbwebdesigns\blogcms\PostCommments\Comment
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
     * @return \rbwebdesigns\blogcms\PostCommments\Comment[]
     */
    public function getCommentsByBlog($blogID, $limit=0)
    {
        $tp = TBL_POSTS;
        $tu = TBL_USERS;
        $sql = "SELECT tc.*, tu.id as userid, tu.username, tp.title, tp.link
            FROM $this->tableName as tc, $tp as tp, $tu as tu
            WHERE tc.post_id = tp.id
            AND tc.user_id = tu.id
            AND tc.blog_id='{$blogID}'
            ORDER BY tc.timestamp DESC";
        
        if ($limit > 0) $sql.= ' LIMIT '. Sanitize::int($limit);
        
        $statement = $this->db->query($sql);
        return $statement->fetchAll(\PDO::FETCH_CLASS, $this->subClass);
    }
    
    /**
     * Get all comments made on a post
     * 
     * @param int $postID
     * @param bool $includeApprovals
     *   Include comments awaiting approval?
     * 
     * @return \rbwebdesigns\blogcms\PostCommments\Comment[]
     */
    public function getCommentsByPost($postID, $includeApprovals=true)
    {
        $query_string = "SELECT c.*, u.name, u.username, CONCAT(u.name, ' ', u.surname) as fullname
            FROM {$this->tableName} as c, users as u
            WHERE u.id = c.user_id
            AND post_id='{$postID}'";

        if (!$includeApprovals) $query_string .= ' AND approved = 1';
        $statement = $this->db->query($query_string);

        return $statement->fetchAll(\PDO::FETCH_CLASS, $this->subClass);
    }

    /**
     * Get all comments made by a user
     * 
     * @param int $userID
     * @param bool $includeApprovals
     *   Include comments awaiting approval?
     * 
     * @return \rbwebdesigns\blogcms\PostCommments\Comment[]
     */
    public function getCommentsByUser($userID, $includeApprovals=true)
    {
        $sql = "SELECT c.*, u.name, u.username, p.title, p.link, CONCAT(u.name, ' ', u.surname) as fullname
            FROM {$this->tableName} as c, users as u, posts as p
            WHERE c.user_id = u.id
            AND p.id = c.post_id
            AND u.id = {$userID}";

        if (!$includeApprovals) $sql .= ' AND approved = 1';
        $statement = $this->db->query($sql);

        return $statement->fetchAll(\PDO::FETCH_CLASS, $this->subClass);
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

}
