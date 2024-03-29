<?php
namespace HamletCMS\Contributors\model;

use rbwebdesigns\core\model\RBFactory;
use rbwebdesigns\core\JSONHelper;
use rbwebdesigns\core\Sanitize;

/**
 * /app/model/mdl_contributorgroups.inc.php
 * 
 * @author R Bertram <ricky@rbwebdesigns.co.uk>
 */

class ContributorGroups extends RBFactory
{
    protected $db;

    /** @var string Class alias for Hamlet model map */
    public static $alias = 'contributorgroups';
    
    public function __construct($modelFactory)
    {
        $this->tableName = 'contributorgroups';
        $this->fields = [
            'id'          => 'int',
            'blog_id'     => 'int',
            'name'        => 'string',
            'description' => 'string',
            'data'        => 'string',
            'super'       => 'boolean',
        ];
        $this->subClass = '\\HamletCMS\\Contributors\\ContributorGroup';

        parent::__construct($modelFactory);
    }

    /**
     * Get a contributor group by ID
     * 
     * @return \HamletCMS\Contributors\ContributorGroup
     */
    public function getGroupById($groupID)
    {
        $group = $this->get('*', ['id' => $groupID], '', '', false);
        $group->permissions = JSONHelper::JSONtoArray($group->data);
        return $group;
    }

    /**
     * Get contributor groups by blog ID
     * 
     * @return \HamletCMS\Contributors\ContributorGroup[]
     */
    public function getByBlog($blogID) {
        return $this->get('*', ['blog_id' => $blogID]);
    }

    /**
     * Need at least an admin permission group and a default
     * group for new contributors
     */
    public function createDefaultGroups($blogID)
    {
        return $this->insert([
            'blog_id' => $blogID,
            'name'    => 'Admin',
            'description' => 'Super user with all permissions granted',
            'locked' => 1,
            'super' => 1,
        ]);
    }

}
