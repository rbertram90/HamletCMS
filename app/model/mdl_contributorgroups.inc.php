<?php
namespace rbwebdesigns\blogcms\model;

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

    const GROUP_CREATE_POSTS        = 'create_posts';
    const GROUP_PUBLISH_POSTS       = 'publish_posts';
    const GROUP_EDIT_POSTS          = 'edit_all_posts';
    const GROUP_DELETE_POSTS        = 'delete_posts';
    const GROUP_MANAGE_COMMENTS     = 'manage_comments';
    const GROUP_DELETE_FILES        = 'delete_files';
    const GROUP_CHANGE_SETTINGS     = 'change_settings';
    const GROUP_MANAGE_CONTRIBUTORS = 'manage_contributors';

    public function __construct($modelFactory)
    {
        $this->tableName = 'contributorgroups';
        $this->fields = [
            'id'          => 'int',
            'blog_id'     => 'int',
            'name'        => 'string',
            'description' => 'string',
            'data'        => 'string',
        ];

        parent::__construct($modelFactory);
    }

    public function getGroupById($groupID)
    {
        $group = $this->get('*', ['id' => $groupID], '', '', false);
        $group['permissions'] = JSONHelper::JSONtoArray($group['data']);
        return $group;
    }

    /**
     * Need at least an admin permission group and a default
     * group for new contributors
     */
    public function createDefaultGroups($blogID)
    {
        $insert = $this->insert([
            'blog_id' => $blogID,
            'name'    => 'Admin',
            'description' => 'Super user with all permissions granted',
            'data' => '{
                "create_posts": 1,
                "publish_posts": 1,
                "edit_all_posts": 1,
                "delete_posts": 1,
                "manage_comments": 1,
                "delete_files": 1,
                "change_settings": 1,
                "manage_contributors": 1
            }',
            'locked' => 1,
        ]);

        if (!$insert) return false;
        
        return $this->insert([
            'blog_id' => $blogID,
            'name'    => 'Default',
            'description' => 'Default permissions for new contributors',
            'data' => '{
                "create_posts": 1,
                "publish_posts": 1,
                "edit_all_posts": 1,
                "delete_posts": 1,
                "manage_comments": 1,
                "delete_files": 1,
                "change_settings": 0,
                "manage_contributors": 0
            }',
            'locked' => 0,
        ]);
    }
}