<?php

namespace rbwebdesigns\blogcms;

class Contributors
{
    public function onGenerateMenu($args)
    {
        if ($args['id'] == 'bloglist') {

            $link = new MenuLink();
            $link->url = BlogCMS::route('contributors.manage', [
                'BLOG_ID' => $args['blog']['id']
            ]);
            $link->text = 'Contributors';
            $args['menu']->addLink($link);
        }
    }

    /**
     * Run database setup
     */
    public function install()
    {
        $dbc = BlogCMS::databaseConnection();

        $dbc->query("CREATE TABLE `contributorgroups` (
            `id` int(11) NOT NULL,
            `blog_id` bigint(15) NOT NULL,
            `name` varchar(50) NOT NULL,
            `description` varchar(500) NOT NULL,
            `data` text NOT NULL,
            `locked` tinyint(1) NOT NULL DEFAULT '0',
            `super` tinyint(1) NOT NULL DEFAULT '0'
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        $dbc->query("CREATE TABLE `contributors` (
            `user_id` int(10) NOT NULL,
            `blog_id` bigint(10) NOT NULL,
            `group_id` int(11) NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        $dbc->query("ALTER TABLE `contributorgroups` ADD PRIMARY KEY (`id`);");
        $dbc->query("ALTER TABLE `contributors` ADD PRIMARY KEY (`user_id`,`blog_id`);");
    }
}