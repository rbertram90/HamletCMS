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
    protected $db, $dbc, $tableName;

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
    
    // Get stored information on a single blog - DEPRECATED! should use $modelcomment->get(array('id'=>'45093870'));
    public function getCommentById($commentid)
    {
        $query_string = 'SELECT * FROM '.$this->tableName.' WHERE id="'.$commentid.'"';
        return $this->db->selectSingleRow($query_string);
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
    
    // Get all the comments from $post - DEPRECATED! should use $modelcomment->get(array('post_id'=>'45093870'));
    public function getCommentsByPost($post, $includeApprovals=true)
    {
        $query_string = 'SELECT * FROM ' . $this->tableName . ' WHERE post_id="'.$post.'"';
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
    public function addComment($pComment, $postid, $blogid, $userid)
    {
        if($postid)
        $query_string = 'INSERT INTO '.$this->tableName.'(message,blog_id,post_id,timestamp,user_id) VALUES ("'.$pComment.'","'.$blogid.'","'.$postid.'","'.date("Y-m-d H:i:s").'","'.$userid.'")';
        return $this->db->runQuery($query_string);
    }
    
    // Delete an existing comment - should there be more checking here?
    public function delete($commentID)
    {
        return $this->db->deleteRow($this->tableName, array('id' => $commentID));
    }
    
    public function approve($commentID)
    {
        return $this->db->updateRow($this->tableName, array('id' => $commentID), array('approved' => 1));
    }
    
    // Update a comment
    public function updateComment()
    {
        
    }
}
