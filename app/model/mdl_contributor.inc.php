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
    public function isBlogContributor($userID, $blogID)
    {
        return $this->count([
            'blog_id' => Sanitize::int($blogID),
            'user_id' => Sanitize::int($userID)
        ]) > 0;
    }

    /**
     * @return array|boolean
     *  All permissions for a user on a blog e.g
     *   create_posts => 1
     *   publish_posts => 0
     *   edit_all_posts => 0
     *   delete_posts => 0
     *   manage_comments => 0
     *   delete_files => 0
     *   change_settings => 0
     *   manage_contributors => 0
     *  Returns false if failed to get permissions
     */
    public function getUserPermissions($userID, $blogID)
    {
        $groupQuery = $this->get('group_id', [
            'user_id' => $userID,
            'blog_id' => $blogID,
        ], '', '', false);

        if (!$groupQuery) return false;

        $groupQuery = $this->db->query("SELECT `data` FROM {$this->tableGroups} WHERE id={$groupQuery['group_id']}");

        if($group = $groupQuery->fetch(\PDO::FETCH_ASSOC)) {
            $userPermissions = JSONHelper::JSONtoArray($group['data']);
            $userPermissions['is_contributor'] = $this->isBlogContributor($userID, $blogID);
            return $userPermissions;
        }

        return false;
    }

    /**
     * @param int    $blogID
     * @param int    $userID
     * @param string|array $permissionName
     */
    public function userHasPermission($userID, $blogID, $permissionName)
    {
        if (!$permissions = $this->getUserPermissions($userID, $blogID)) {
            return false;
        }

        if (gettype($permissionName) == 'string') {
            return isset($permissions[$permissionName]) && $permissions[$permissionName];
        }
        elseif (gettype($permissionName) == 'array') {
            foreach ($permissionName as $requiredPermission) {
                if (!isset($permissions[$requiredPermission]) || !$permissions[$requiredPermission]) {
                    return false;
                }
            }
            return true;
        }
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
    
}
