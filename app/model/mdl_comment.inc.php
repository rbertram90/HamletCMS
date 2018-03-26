<?php
namespace rbwebdesigns\blogcms\model;

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
        $this->tableName = TBL_COMMENTS;

        $this->fields = array(
            'id' => 'number',
            'message' => 'memo',
            'blog_id' => 'number',
            'post_id' => 'number',
            'timestamp' => 'datetime',
            'user_id' => 'number'
        );
    }
    
    // Get stored information on a single blog
    public function getCommentById($commentid)
    {
        return $this->get('*', ['id' => $commentid], '', '', false);
    }
    
    // Get all the posts from $blog
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
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    // Get all the comments from $post
    public function getCommentsByPost($post, $includeApprovals=true)
    {
        $query_string = 'SELECT c.*, u.name, u.username, CONCAT(u.name, \'\', u.surname) as fullname FROM ' . $this->tableName . ' as c, users as u WHERE u.id = c.user_id AND post_id="'.$post.'"';

        if(!$includeApprovals) $query_string .= ' AND approved = 1';
        $statement = $this->db->query($query_string);

        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    // Count the number of comments for this post
    function countPostComments($postid)
    {
        return $this->db->countRows($this->tableName, array('post_id' => $postid));
    }
    
    
    // Count the total number of comments made for all posts on a blog - NO LONGER NEEDED - USE $modelcomments->getCount();
    function countBlogComments()
    {
    
    }
    
    // Create a new comment
    public function addComment($comment, $postid, $blogid, $userid)
    {
        return $this->insert([
            'message' => $comment,
            'blog_id' => $blogid,
            'post_id' => $postid,
            'timestamp' => date("Y-m-d H:i:s"),
            'user_id' => $userid,
        ]);
    }
    
    // Delete an existing comment - should there be more checking here?
    public function deleteComment($commentID)
    {
        return $this->delete(['id' => $commentID]);
    }
    
    public function approve($commentID)
    {
        return $this->update(['id' => $commentID], ['approved' => 1]);
    }

}
