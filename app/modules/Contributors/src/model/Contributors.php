<?php
namespace rbwebdesigns\blogcms\Contributors\model;

use rbwebdesigns\core\model\RBFactory;
use rbwebdesigns\core\Sanitize;
use rbwebdesigns\core\JSONHelper;
use rbwebdesigns\blogcms\BlogCMS;

/**
 * /app/model/mdl_contributor.inc.php
 */

class Contributors extends RBFactory
{
    protected $db;
    protected $tblbloguser;

    public function __construct($modelFactory)
    {
        $this->db = $modelFactory->getDatabaseConnection();
        $this->tableName = TBL_CONTRIBUTORS;
        $this->tableGroups = 'contributorgroups';
        $this->subClass = '\\rbwebdesigns\\blogcms\\Contributors\\Contributor';

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
        return $results->fetchAll(\PDO::FETCH_CLASS, '\\rbwebdesigns\\blogcms\\Blog\\Blog');
    }
    
    // Get all users that can contribute to a $blog
    public function getBlogContributors($blogid)
    {
        $query_string = 'SELECT a.group_id, (SELECT `name` FROM contributorgroups WHERE id=a.group_id) as groupname, b.* FROM '.$this->tableName.' as a LEFT JOIN '.$this->tblusers.' as b ON a.user_id = b.id WHERE a.blog_id='.$blogid;
        $statement = $this->db->query($query_string);
        return $statement->fetchAll(\PDO::FETCH_CLASS, $this->subClass);
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
    public function isBlogContributor($userID, $blogID)
    {
        return $this->count([
            'blog_id' => Sanitize::int($blogID),
            'user_id' => Sanitize::int($userID)
        ]) > 0;
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
    public function addBlogContributor($userID, $blogID, $groupID)
    {
        return $this->insert([
            'user_id'  => $userID,
            'group_id' => $groupID,
            'blog_id'  => $blogID,
        ]);
    }
    
    /**
     * Security functions - check Read, Write Permissions
     * Should this be in contributors model?
     * 
     * @param int $blogID
     */
    public function canWrite($blogID)
    {
        // Only allow contributors to update the blog settings
        // further 'custom restrictions' to be added
        $currentUser = BlogCMS::session()->currentUser;

        $rowCount = $this->count([
            'blog_id' => $blogID,
            'user_id' => $currentUser['id']
        ]);
        
        return $rowCount > 0;
    }

}
