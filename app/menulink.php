<?php

namespace rbwebdesigns\blogcms;

/**
 * This class provides a structure for menu items within blog cms
 * it does not provide any implementation of the markup that will
 * be shown in the final output. This is left to the template file
 * to loop over all the links in the menu.
 * 
 * @author Ricky Bertram <ricky@rbwebdesigns.co.uk>
 */
class MenuLink
{
    /**
     * @var string URL to open
     */
    public $url;
    /**
     * @var string Link text
     */
    public $text;
    /**
     * @var string Link descriptive text
     */
    public $subtext;
    /**
     * @var string Target window for anchor
     */
    public $target = '_self';
    /**
     * @var array Permissions required to use this link
     *   for list of permissions see constants in class ContributorGroups
     */
    public $permissions = [];
    /**
     * @var bool Is this menu item currently active on page
     */
    public $active = false;
    /**
     * @var string Link can have an icon associated with it (implementation handled by view)
     */
    public $icon;

    /**
     * @return bool Does the user have permission to use this link
     */
    public function accessible()
    {
        if (count($this->permissions) == 0) return true;

        if (!BlogCMS::$blogID) {
            trigger_error('No Blog ID was set in CMS core when checking menu item permissions', E_USER_WARNING);
            return false;
        }

        $modelPermissions = BlogCMS::model('\rbwebdesigns\blogcms\Contributors\model\Permissions');
        return $modelPermissions->userHasPermission($this->permissions);
    }

}