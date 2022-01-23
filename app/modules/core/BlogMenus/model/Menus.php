<?php

namespace HamletCMS\BlogMenus\model;

use rbwebdesigns\core\model\RBFactory;

class Menus extends RBFactory {

    /** @var string Class alias for Hamlet model map */
    public static $alias = 'menus';

    function __construct($modelManager)
    {
        $this->tableName = 'menus';
        $this->subClass = '\HamletCMS\BlogMenus\BlogMenu';
        $this->fields = [
            'id' => 'number',
            'name' => 'string',
            'blog_id' => 'number',
        ];

        parent::__construct($modelManager);
    }

    /**
     * Get a menu by primary key.
     * 
     * @param int
     * 
     * @return \HamletCMS\BlogMenus\BlogMenu
     */
    public function getMenuById($menuID)
    {
        return $this->get('*', ['id' => $menuID], null, null, false);
    }

    /**
     * Get menus by blog.
     * 
     * @param \HamletCMS\blog\Blog $blog
     * 
     * @return \HamletCMS\BlogMenus\BlogMenu[]
     */
    public function getMenusByBlog($blog) {
        return $this->get('*', ['blog_id' => $blog->id]);
    }
    
}
