<?php

namespace HamletCMS\Contributors\model;

use HamletCMS\HamletCMS;
use rbwebdesigns\core\model\RBFactory;
use rbwebdesigns\core\JSONHelper;

class Permissions extends RBFactory
{
    /** @var string Class alias for Hamlet model map */
    public static $alias = 'permissions';

    /**
     * @var mixed[] Cached list of all the permissions that have
     *   been requested to reduce DB load.
     */
    protected $usersPermissionCache = [];

    public function __construct($modelFactory)
    {
        parent::__construct($modelFactory);
        $this->tableName = 'contributors';
        $this->subClass = '\\HamletCMS\\Contributors\\Contributor';
        $this->modelContributors = HamletCMS::model('contributors');
        $this->modelContributorGroups = HamletCMS::model('contributorgroups');
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
     * 
     * @param int|string $blogID
     * @param int|string $userID
     * 
     * @return int
     *   ID for the group a user is in for a blog
     */
    public function getUserGroup($blogID, $userID=false)
    {
        $userID = $userID ?: HamletCMS::session()->currentUser['id'];
        
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
     * 
     * @param string|string[] List of permissions, or single permission to check.
     * @param int|string $blogID
     * @param int|string $userID
     * 
     * @return boolean
     *   true if user has permission, false otherwise.
     */
    public function userHasPermission($requiredPermissions, $blogID = 0, $userID = false)
    {
        $userID = $userID ?: HamletCMS::session()->currentUser['id'];
        if (gettype($requiredPermissions) === 'string') $requiredPermissions = [$requiredPermissions];
        if (count($requiredPermissions) === 0) return true; // no permissions required
        
        if ($blogID == 0) $blogID = HamletCMS::$blogID ?? HamletCMS::$blog->id;
        if (!$blogID) return false; // this is some sort of error state!

        // Search request cache.
        $permissionsPassed = 0;
        foreach ($requiredPermissions as $permission) {
            $cacheKey = $userID . '_' . $blogID . '_' . $permission;
            if (array_key_exists($cacheKey, $this->usersPermissionCache) &&
                $this->usersPermissionCache[$cacheKey] === 0) {
                return false;
            }
            elseif (array_key_exists($cacheKey, $this->usersPermissionCache)) {
                $permissionsPassed++;
            }
        }

        // All permissions queried already.
        if ($permissionsPassed === count($requiredPermissions)) return true;

        // check if users is in a contributor group for this blog.
        if (!$groupID = $this->getUserGroup($blogID)) return false;
        
        $group = $this->modelContributorGroups->getGroupById($groupID);

        // Admin override for all permissions.
        if ($group->super == 1) return true;

        $userPermissions = JSONHelper::JSONtoArray($group->data);
        $userPermissions['is_contributor'] = $this->modelContributors->isBlogContributor($userID, $blogID);

        foreach ($requiredPermissions as $permission) {
            $cacheKey = $userID . '_' . $blogID . '_' . $permission;
            if (!array_key_exists($permission, $userPermissions) || 
              $userPermissions[$permission] == 0) {
                $this->usersPermissionCache[$cacheKey] = 0;
                return false;
            }
            $this->usersPermissionCache[$cacheKey] = 1;
        }
        return true;
    }

}
