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
    
    // Get all blogs a user can contribute too
    public function getContributedBlogs($userid)
    {
        // Get all the blog id for this user
        $query_string = 'SELECT a.blog_id, b.* FROM '.$this->tableName.' as a LEFT JOIN '.$this->tblblogs.' as b ON a.blog_id = b.id WHERE a.user_id='.$userid;
        $results = $this->db->query($query_string);
        return $results->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    // Get all users that can contribute to a $blog
    public function getBlogContributors($blogid)
    {
        $query_string = 'SELECT a.privileges, b.* FROM '.$this->tableName.' as a LEFT JOIN '.$this->tblusers.' as b ON a.user_id = b.id WHERE a.blog_id='.$blogid;
        $statement = $this->db->query($query_string);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    // Check if user is the blog owner
    public function isBlogOwner($userid, $blogid)
    {
        return  $this->db->countRows($this->tblblogs, ['user_id' => $userid, 'id' => $blogid]) >= 1;
    }
    
    /**
     * Determine if a user is already on the contributor list for a blog
     * 
     * @param int $blog
     * @param int $user
     * @param string $privilageLevel
     * 
     * @return bool Is the user and contributor to the blog?
     */
    public function isBlogContributor($blogID, $userID, $privilegeLevel='')
    {
        $where = [
            'blog_id' => Sanitize::int($blogID),
            'user_id' => Sanitize::int($userID)
        ];
        if ($privilegeLevel !== '') $where['privileges'] = $privilegeLevel;

        return $this->count($where) > 0;
    }
    
    /**
     * Add a new contributor to a blog
     * 
     * @param int $userID
     * @param string $access
     * @param int $blogID
     * 
     * @return bool Was the insert successful
     */
    public function addBlogContributor($userID, $access, $blogID)
    {
        if ($this->isBlogContributor($blogID, $userID)) return false;

        return $this->insert([
            'user_id' => $userID,
            'privileges' => (strtolower($access) == "a") ? "all" : "postonly",
            'blog_id' => $blogID,
        ]);
    }
    
    /**
     * Update (11/08/2014)
     */
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