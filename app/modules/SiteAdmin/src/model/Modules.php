<?php

namespace rbwebdesigns\blogcms\SiteAdmin\model;

use rbwebdesigns\core\model\RBFactory;

class Modules extends RBFactory
{
    protected $db, $tableName;

    /**
     * @param \rbwebdesigns\core\model\ModelManager $modelFactory
     */
    function __construct($modelFactory)
    {
        $this->db = $modelFactory->getDatabaseConnection();
        $this->tableName = 'modules';
    }

    public function getList()
    {
        return $this->db->selectAllRows($this->tableName, '*', 'name ASC');
    }
}