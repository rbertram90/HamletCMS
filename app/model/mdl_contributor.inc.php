<?php
namespace rbwebdesigns\blogcms;
use rbwebdesigns;

class ClsContributors extends rbwebdesigns\RBmodel {
    
    protected $db;
    protected $dbc;
    protected $tblbloguser;
    protected $tblname;
    
    public function __construct($db) {
        $this->db = $db;
        $this->dbc = $this->db->getConnection();
        $this->tblname = TBL_CONTRIBUTORS;
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
        $query_string = 'SELECT a.blog_id, b.* FROM '.$this->tblname.' as a LEFT JOIN '.$this->tblblogs.' as b ON a.blog_id = b.id WHERE a.user_id='.$userid;
		$results = $this->db->runQuery($query_string);
        return $results->fetchAll(\PDO::FETCH_ASSOC);
    }
	
    
    // Get all users that can contribute to a $blog
    public function getBlogContributors($blogid) {
        $query_string = 'SELECT a.privileges, b.* FROM '.$this->tblname.' as a LEFT JOIN '.$this->tblusers.' as b ON a.user_id = b.id WHERE a.blog_id='.$blogid;
        return $this->db->select_multi($query_string);
    }
    
    
    // Check if user is the blog owner - note this should probabily be a seperate permission!
    public function isBlogOwner($userid, $blogid) {
        $liCount = $this->db->countRows($this->tblblogs, array('user_id' => $userid, 'id' => $blogid));
        return ($liCount >= 1);
    }
    
    /**
        Determine if a $user is already on the contributor list for a $blog
    **/
    public function isBlogContributor($intBlog, $intUser, $privilegeLevel='') {
        
        // Create and execute query
        $arrWhere = array(
			'blog_id' => safeNumber($intBlog),
			'user_id' => safeNumber($intUser)
		);
        if($privilegeLevel !== '') $arrWhere['privileges'] = $privilegeLevel;
        
        $result = $this->db->selectSingleRow($this->tblname, 'count(*) as matches', $arrWhere);
        
        // Interpret Results
        return ($result['matches'] > 0);
    }
    
    
    /**
        Insert
    **/
    
    // Add a new $contributor to a $blog
    public function addBlogContributor($puser, $paccess, $pblog) {
	
        $liUser = safeNumber($puser);
        $liBlog = safeNumber($pblog);
        $lsAccess = (strtolower($paccess) == "a") ? "all" : "postonly";
		
        // Check the user doesn't already contribute to this blog
        if($this->isBlogContributor($liBlog, $liUser)) {
            return "Unable to add contributor - User already found in contributor list";
        }
        $query_string = "INSERT INTO ".$this->tblname." (user_id, privileges, blog_id) VALUES ('$liUser', '$lsAccess', '$liBlog')";
        return $this->db->runQuery($query_string);
    }
    
    
    /**
        Update (11/08/2014)
    **/
    public function changePermissions($userid, $blogid, $permission) {
        $blogid = sanitize_number($blogid);
        $userid = sanitize_number($userid);
        if($permission !== 'all' && $permission !== 'postonly') return false;        
        if(!$this->isBlogContributor($blogid, $_SESSION['userid'], 'all') || $this->isBlogOwner($userid, $blogid)) return false;        
        return $this->db->updateRow($this->tblname, array('user_id' => $userid, 'blog_id' => $blogid), array('privileges' => $permission));
    }
    
    
    /**
        Delete (11/08/2014) - DEPRECATED 23/08/2014! - use $contribs->delete(array('userid'=>'234234','blogid'=>'54320957843'));
    
    public function delete($userid, $blogid) {
        // Last chance catch!
        $blogid = sanitize_number($blogid);
        $userid = sanitize_number($userid);
        if(!$this->isBlogContributor($blogid, $_SESSION['userid'], 'all') || $this->isBlogOwner($userid, $blogid)) return false;
        return $this->db->deleteRow($this->tblname, array('user_id' => $userid, 'blog_id' => $blogid));
    }
    **/
}
?>