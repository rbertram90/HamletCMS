<?php
namespace rbwebdesigns\blogcms\model;

use rbwebdesigns\core\model\RBFactory;
use rbwebdesigns\core\Sanitize;

/**
 * /app/model/mdl_contributor.inc.php
 */

class Contributors extends RBFactory
{
    protected $db;
    protected $tblbloguser;
    protected $tableName;
    
    public function __construct($modelFactory)
    {
        $this->db = $modelFactory->getDatabaseConnection();
        $this->tableName = TBL_CONTRIBUTORS;

        $this->tblusers = TBL_USERS;
        $this->tblblogs = TBL_BLOGS;
        $this->fields = array(
            'user_id' => 'number',
            'blog_id' => 'number',
            'privileges' => 'string'
        );
    }
    
    /**
        Select
    **/
    
    // Get all blogs a user can contribute too
    public function getContributedBlogs($userid) {
        // Get all the blog id for this user
        $query_string = 'SELECT a.blog_id, b.* FROM '.$this->tableName.' as a LEFT JOIN '.$this->tblblogs.' as b ON a.blog_id = b.id WHERE a.user_id='.$userid;
        $results = $this->db->query($query_string);
        return $results->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    
    // Get all users that can contribute to a $blog
    public function getBlogContributors($blogid) {
        $query_string = 'SELECT a.privileges, b.* FROM '.$this->tableName.' as a LEFT JOIN '.$this->tblusers.' as b ON a.user_id = b.id WHERE a.blog_id='.$blogid;
        $statement = $this->db->query($query_string);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    
    // Check if user is the blog owner - note this should probabily be a seperate permission!
    public function isBlogOwner($userid, $blogid) {
        $liCount = $this->db->countRows($this->tblblogs, array('user_id' => $userid, 'id' => $blogid));
        return ($liCount >= 1);
    }
    
    /**
     * Determine if a $user is already on the contributor list for a $blog
     */
    public function isBlogContributor($intBlog, $intUser, $privilegeLevel='')
    {
        // Create and execute query
        $arrWhere = array(
            'blog_id' => Sanitize::int($intBlog),
            'user_id' => Sanitize::int($intUser)
        );
        if($privilegeLevel !== '') $arrWhere['privileges'] = $privilegeLevel;
        
        $result = $this->db->selectSingleRow($this->tableName, 'count(*) as matches', $arrWhere);
        
        // Interpret Results
        return ($result['matches'] > 0);
    }
    
    
    /**
        Insert
    **/
    
    // Add a new $contributor to a $blog
    public function addBlogContributor($puser, $paccess, $pblog) {
    
        $liUser = Sanitize::int($puser);
        $liBlog = Sanitize::int($pblog);
        $lsAccess = (strtolower($paccess) == "a") ? "all" : "postonly";
        
        // Check the user doesn't already contribute to this blog
        if($this->isBlogContributor($liBlog, $liUser)) {
            return "Unable to add contributor - User already found in contributor list";
        }
        $query_string = "INSERT INTO ".$this->tableName." (user_id, privileges, blog_id) VALUES ('$liUser', '$lsAccess', '$liBlog')";
        return $this->db->query($query_string);
    }
    
    
    /**
        Update (11/08/2014)
    **/
    public function changePermissions($userid, $blogid, $permission) {
        $blogid = Sanitize::int($blogid);
        $userid = Sanitize::int($userid);
        if($permission !== 'all' && $permission !== 'postonly') return false;        
        if(!$this->isBlogContributor($blogid, $_SESSION['userid'], 'all') || $this->isBlogOwner($userid, $blogid)) return false;        
        return $this->db->updateRow($this->tableName, array('user_id' => $userid, 'blog_id' => $blogid), array('privileges' => $permission));
    }
    
    
    /**
        Delete (11/08/2014) - DEPRECATED 23/08/2014! - use $contribs->delete(array('userid'=>'234234','blogid'=>'54320957843'));
    
    public function delete($userid, $blogid) {
        // Last chance catch!
        $blogid = Sanitize::int($blogid);
        $userid = Sanitize::int($userid);
        if(!$this->isBlogContributor($blogid, $_SESSION['userid'], 'all') || $this->isBlogOwner($userid, $blogid)) return false;
        return $this->db->deleteRow($this->tableName, array('user_id' => $userid, 'blog_id' => $blogid));
    }
    **/
}
?>