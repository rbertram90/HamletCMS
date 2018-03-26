<?php
namespace rbwebdesigns\blogcms\model;

use rbwebdesigns\core\model\RBFactory;
use rbwebdesigns\core\Sanitize;
use rbwebdesigns\core\JSONHelper;

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
        $this->tableGroups = 'contributorgroups';

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
        $query_string = 'SELECT a.group_id, (SELECT `name` FROM contributorgroups WHERE id=a.group_id) as groupname, b.* FROM '.$this->tableName.' as a LEFT JOIN '.$this->tblusers.' as b ON a.user_id = b.id WHERE a.blog_id='.$blogid;
        $statement = $this->db->query($query_string);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    // Check if user is the blog owner
    public function isBlogOwner($userid, $blogid)
    {
        return  $this->db->countRows($this->tblblogs, ['user_id' => $userid, 'id' => $blogid]) >= 1;
    }
    
    /**
     * Determine if a user is a contributor in some form for a blog
     * 
     * @param int $blogID
     * @param int $userID
     * 
     * @return bool Is the user and contributor to the blog?
     */
    public function isBlogContributor($blogID, $userID)
    {
        return $this->count([
            'blog_id' => Sanitize::int($blogID),
            'user_id' => Sanitize::int($userID)
        ]) > 0;
    }

    /**
     * @param int    $blogID
     * @param int    $userID
     * @param string $permissionName
     */
    public function userHasPermission($userID, $blogID, $permissionName)
    {
        $groupQuery = $this->get('group_id', [
            'user_id' => $userID,
            'blog_id' => $blogID,
        ], '', '', false);

        if (!$groupQuery) return false;

        $groupQuery = $this->db->query("SELECT `data` FROM {$this->tableGroups} WHERE id={$groupQuery['group_id']}");

        if($group = $groupQuery->fetch(\PDO::FETCH_ASSOC)) {
            $data = JSONHelper::JSONtoArray($group['data']);
            return isset($data[$permissionName]) && $data[$permissionName];
        }
        return false;
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
        return $this->insert([
            'user_id'    => $userID,
            'privileges' => (strtolower($access) == "a") ? "all" : "postonly",
            'blog_id'    => $blogID,
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

}
