<?php

namespace HamletCMS\Contributors\model;

use HamletCMS\HamletCMS;
use rbwebdesigns\core\model\RBFactory;
use rbwebdesigns\core\JSONHelper;

class Permissions extends RBFactory
{
    protected $db;

    protected $subClass;

    /** @var string Class alias for Hamlet model map */
    public static $alias = 'permissions';

    public function __construct($modelFactory)
    {
        $this->db = $modelFactory->getDatabaseConnection();
        $this->tableName = 'contributors';
        $this->subClass = '\\HamletCMS\\Contributors\\Contributor';

        $this->modelContributors = HamletCMS::model('\HamletCMS\Contributors\model\Contributors');
        $this->modelContributorGroups = HamletCMS::model('\HamletCMS\Contributors\model\ContributorGroups');
    }

    /**
     * Get the list of all enabled permissions on the system
     * 
     * @return array
     */
    public static function getList()
    {
        if ($cache = HamletCMS::getCache('permissions')) {
            return $cache;
        }

        HamletCMS::generatePermissionCache();

        return HamletCMS::getCache('permissions');
    }

    /**
     * Get the group ID a user belongs to for a blog
     */
    public function getUserGroup($blogID)
    {
        $userID = HamletCMS::session()->currentUser['id'];
        
        if (!$userID) return false;

        // Get the group ID for a user
        $groupQuery = $this->get('group_id', [
            'user_id' => $userID,
            'blog_id' => $blogID,
        ], '', '', false);

        if (!$groupQuery) return false;

        return $groupQuery->group_id;
    }

    /**
     * Check if the user has permission to perform an action
     */
    public function userHasPermission($requiredPermissions, $blogID = 0)
    {
        if (gettype($requiredPermissions) == 'string') $requiredPermissions = [$requiredPermissions];
        if (count($requiredPermissions) == 0) return true;

        if ($blogID == 0) $blogID = HamletCMS::$blogID;
        if (!$groupID = $this->getUserGroup($blogID)) return false;
        
        $group = $this->modelContributorGroups->getGroupById($groupID);
        
        // Override for all permissions
        if ($group->super == 1) return true;

        $userID = HamletCMS::session()->currentUser['id'];
        $userPermissions = JSONHelper::JSONtoArray($group->data);
        $userPermissions['is_contributor'] = $this->modelContributors->isBlogContributor($userID, $blogID);

        foreach ($requiredPermissions as $permission) {
            if (!array_key_exists($permission, $userPermissions) || 
              $userPermissions[$permission] == 0) {
                return false;
            }
        }
        return true;
    }

}