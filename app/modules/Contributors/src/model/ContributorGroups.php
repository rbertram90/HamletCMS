<?php
namespace rbwebdesigns\blogcms\Contributors\model;

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
    protected $tableName;
    
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
        $this->subClass = '\\rbwebdesigns\\blogcms\\Contributors\\ContributorGroup';

        parent::__construct($modelFactory);
    }

    public function getGroupById($groupID)
    {
        $group = $this->get('*', ['id' => $groupID], '', '', false);
        $group->permissions = JSONHelper::JSONtoArray($group->data);
        return $group;
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