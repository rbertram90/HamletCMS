<?php

namespace rbwebdesigns\HamletCMS;

class BlogMenus
{

    /**
     * Create database structure
     */
    public function install()
    {
        $dbc = HamletCMS::databaseConnection();
        
        $dbc->query("CREATE TABLE `menus` (
            `id` int(11) NOT NULL,
            `name` varchar(100) NOT NULL,
            `blog_id` bigint(10) NOT NULL,
            `sort` enum('text','custom') NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        $dbc->query("CREATE TABLE `menuitems` (
          `id` int(11) NOT NULL,
          `text` varchar(60) NOT NULL,
          `type` enum('post','tag','external','mail','tel','blog') NOT NULL,
          `link_target` varchar(255) NOT NULL,
          `menu_id` int(11) NOT NULL,
          `weight` int(11) NOT NULL,
          `new_window` tinyint(1) NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");
        
        $dbc->query("ALTER TABLE `menuitems` ADD PRIMARY KEY (`id`), ADD KEY `menu_id` (`menu_id`);");
        $dbc->query("ALTER TABLE `menus` ADD PRIMARY KEY (`id`), ADD KEY `blog_id` (`blog_id`);");

        $dbc->query("ALTER TABLE `menus` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
        $dbc->query("ALTER TABLE `menuitems` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");

        $dbc->query("ALTER TABLE `menus` ADD CONSTRAINT `Blog` FOREIGN KEY (`blog_id`) REFERENCES `blogs`(`id`) ON DELETE CASCADE;");
        $dbc->query("ALTER TABLE `menuitems` ADD CONSTRAINT `Menu` FOREIGN KEY (`menu_id`) REFERENCES `menus`(`id`) ON DELETE CASCADE;");
    }

    /**
     * Removes all traces of menus from the database
     */
    public function uninstall()
    {
        $dbc = HamletCMS::databaseConnection();
        $dbc->query("DROP TABLE IF EXISTS `menuitems`;");
        $dbc->query("DROP TABLE IF EXISTS `menus`;");
    }

    /**
     * Run unit tests
     */
    public function runUnitTests($args) {
        // This ensures that the blog tests have been run and we've
        // got an active blog in HamletCMS::getActiveBlog()
        if ($args['context'] === 'blog') {
            $createBlogTest = new BlogMenus\tests\CreateMenuTest();
            $createBlogTest->run();

            /** @var \rbwebdesigns\HamletCMS\BlogMenus\model\Menus $model */
            $model = HamletCMS::model('\\rbwebdesigns\\HamletCMS\\BlogMenus\\model\\Menus');
            $newMenu = $model->get('*', ['blog_id' => $args['blogID']], null, null, false);

            // Run next level of tests
            if ($newMenu) {
                HamletCMS::runHook('runUnitTests', ['context' => 'menu', 'menu' => $newMenu]);
            }
        }

        if ($args['context'] === 'menu') {
            // $editMenuItemTest = new BlogMenus\tests\CreateMenuItemTest($args['menu']);

            $createMenuItemTest = new BlogMenus\tests\CreateMenuItemTest($args['menu']);
            $createMenuItemTest->run();
        }

    }

}
