<?php
namespace HamletCMS\Contributors\model;

use rbwebdesigns\core\model\RBFactory;
use rbwebdesigns\core\Sanitize;
use HamletCMS\HamletCMS;

/**
 * Contributors factory model.
 */
class Contributors extends RBFactory
{
    protected $db;
    protected $tblbloguser;

    /** @var string Class alias for Hamlet model map */
    public static $alias = 'contributors';

    public function __construct($modelFactory)
    {
        parent::__construct($modelFactory);

        $this->tableName = TBL_CONTRIBUTORS;
        $this->tableGroups = 'contributorgroups';
        $this->subClass = '\\HamletCMS\\Contributors\\Contributor';

        $this->tblusers = TBL_USERS;
        $this->tblblogs = TBL_BLOGS;
        $this->fields = array(
            'user_id' => 'number',
            'blog_id' => 'number',
            'privileges' => 'string'
        );
    }
    
    /**
     * Get all blogs a user can contribute too
     * 
     * @return \HamletCMS\Blog\Blog[]
     */
    public function getContributedBlogs($userid)
    {
        // Get all the blog id for this user
        $query_string = 'SELECT a.blog_id, b.* FROM '.$this->tableName.' as a LEFT JOIN '.$this->tblblogs.' as b ON a.blog_id = b.id WHERE a.user_id='.$userid;
        $results = $this->db->query($query_string);
        return $results->fetchAll(\PDO::FETCH_CLASS, '\\HamletCMS\\Blog\\Blog');
    }

    /**
     * Get contributors by group
     * 
     * @return \HamletCMS\Contributors\Contributor[]
     */
    public function getByGroup($groupID) {
        return $this->get('*', ['group_id' => $groupID]);
    }
    
    /**
     * Get contributor objects (doesn't load user account data).
     * 
     * @param string|int $blogID
     * @param bool $returnRaw
     *   True to return an associative array of values, false (default)
     *   returns Contributor objects.
     * 
     * @return \HamletCMS\Contributors\Contributor[]|mixed[]
     */
    public function getBlogContributors($blogid, $returnRaw = false)
    {
        if ($returnRaw) {
            return $this->get('*', ['blog_id' => $blogid], '', '', true, true);
        }
        return $this->get('*', ['blog_id' => $blogid]);
    }
    
    /**
     * Check if a user is the owner of a blog.
     * 
     * The user is the owner if they are the one who originally created it.
     * 
     * @todo could do with a option to change ownership?
     * 
     * @return bool
     */
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
        $currentUser = HamletCMS::session()->currentUser;

        $rowCount = $this->count([
            'blog_id' => $blogID,
            'user_id' => $currentUser['id']
        ]);
        
        return $rowCount > 0;
    }

    /**
     * Determine if a user is in a group
     * 
     * @return bool
     */
    public function userIsInGroup($userID, $groupID) {
        return $this->count([
            'user_id' => $userID,
            'group_id' => $groupID
        ]);
    }

}
