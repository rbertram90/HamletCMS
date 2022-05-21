<?php

namespace HamletCMS\Contributors;

use HamletCMS\HamletCMS;
use HamletCMS\MenuLink;

class Module
{
    public function __construct()
    {
        $this->model = HamletCMS::model('\HamletCMS\Contributors\model\Contributors');
    }

    public function onGenerateMenu($args)
    {
        if ($args['id'] == 'bloglist') {

            $link = new MenuLink();
            $link->url = HamletCMS::route('contributors.manage', [
                'BLOG_ID' => $args['blog']->id
            ]);
            $link->text = 'Contributors';
            if ($link->url) {
                $args['menu']->addLink($link);
            }
        }
    }

    /**
     * Run database setup
     */
    public function install()
    {
        $dbc = HamletCMS::databaseConnection();

        $dbc->query("CREATE TABLE `contributorgroups` (
            `id` int(11) NOT NULL,
            `blog_id` bigint(15) NOT NULL,
            `name` varchar(50) NOT NULL,
            `description` varchar(500),
            `data` text,
            `locked` tinyint(1) NOT NULL DEFAULT '0',
            `super` tinyint(1) NOT NULL DEFAULT '0'
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        $dbc->query("CREATE TABLE `contributors` (
            `user_id` int(10) NOT NULL,
            `blog_id` bigint(10) NOT NULL,
            `group_id` int(11) NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        $dbc->query("ALTER TABLE `contributorgroups` ADD PRIMARY KEY (`id`);");
        $dbc->query("ALTER TABLE `contributorgroups` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
        $dbc->query("ALTER TABLE `contributors` ADD PRIMARY KEY (`user_id`,`blog_id`);");
    }

    /**
     * Adds a total contributor count to the blog dashboard
     */
    public function dashboardCounts($args)
    {
        $args['counts']['contributors'] = $this->model->getCount(['blog_id' => $args['blog']->id]);
    }

    public function runUnitTests($args) {
        if ($args['context'] === 'blog') {
            $test = new tests\ContributorsTests();
            $test->blogID = $args['blogID'];
            $test->run();
        }
    }

}