<?php

namespace rbwebdesigns\HamletCMS\SiteAdmin\model;

use rbwebdesigns\core\model\RBFactory;

/**
 * Factory managing Module class
 */
class Modules extends RBFactory
{
    protected $db;
    protected $subClass;

    /**
     * @param \rbwebdesigns\core\model\ModelManager $modelFactory
     */
    function __construct($modelFactory)
    {
        $this->db = $modelFactory->getDatabaseConnection();
        $this->tableName = 'modules';
        $this->subClass = '\\rbwebdesigns\\HamletCMS\\SiteAdmin\\Module';
    }

    public function getList()
    {
        return $this->db->selectAllRows($this->subClass, $this->tableName, '*', 'name ASC');
    }

}