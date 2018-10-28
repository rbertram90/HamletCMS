<?php

namespace rbwebdesigns\blogcms\Contributors\model;

use rbwebdesigns\blogcms\BlogCMS;
use rbwebdesigns\core\model\RBFactory;
use rbwebdesigns\core\JSONHelper;

class Permissions extends RBFactory
{
    protected $db;

    public function __construct($modelFactory)
    {
        $this->db = $modelFactory->getDatabaseConnection();
        $this->tableName = 'contributors';

        $this->modelContributors = BlogCMS::model('\rbwebdesigns\blogcms\Contributors\model\Contributors');
    }

    /**
     * Get the list of all enabled permissions on the system
     * 
     * @return array
     */
    public static function getList()
    {
        if ($cache = BlogCMS::getCache('permissions')) {
            return $cache;
        }

        BlogCMS::generatePermissionCache();

        return BlogCMS::getCache('permissions');
    }

    /**
     * Get the group ID a user belongs to for a blog
     */
    public function getUserGroup($blogID)
    {
        $userID = BlogCMS::session()->currentUser['id'];

        // Get the group ID for a user
        $groupQuery = $this->get('group_id', [
            'user_id' => $userID,
            'blog_id' => $blogID,
        ], '', '', false);

        if (!$groupQuery) return false;

        return $groupQuery['group_id'];
    }

    /**
     * Check if the user has permission to perform an action
     */
    public function userHasPermission($blogID, $permission)
    {
        if (!$groupID = $this->getUserGroup($blogID)) return false;

        $sql = sprintf('SELECT `super`, `data` FROM `contributorgroups` WHERE id=%d', $groupID);
    
        $groupQuery = $this->db->query($sql);
        $groupData = $groupQuery->fetch(\PDO::FETCH_ASSOC);

        // Override for all permissions
        if ($groupData['super'] == 1) return true;

        $userPermissions = JSONHelper::JSONtoArray($group['data']);
        $userPermissions['is_contributor'] = $this->modelContributors->isBlogContributor($userID, $blogID);
        
        return array_key_exists($permission, $userPermissions) && $userPermissions[$permission] == 1;
    }

}