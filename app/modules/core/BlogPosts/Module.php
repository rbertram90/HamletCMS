<?php

namespace HamletCMS\BlogPosts;

use HamletCMS\HamletCMS;
use HamletCMS\MenuLink;

class Module
{
    public function __construct()
    {
        $this->model = HamletCMS::model('\HamletCMS\BlogPosts\model\Posts');
    }

    public function onGenerateMenu($args)
    {
        if ($args['id'] == 'bloglist') {
            $link = new MenuLink();
            $link->url = HamletCMS::route('posts.manage', [
                'BLOG_ID' => $args['blog']->id
            ]);
            $link->text = 'Manage posts';
            $args['menu']->addLink($link);

            $link = new MenuLink();
            $link->url = HamletCMS::route('posts.create', [
                'BLOG_ID' => $args['blog']->id
            ]);
            $link->text = 'Create new post';
            $args['menu']->addLink($link);
        }
    }

    /**
     * Run database setup
     */
    public function install()
    {
        $dbc = HamletCMS::databaseConnection();

        $dbc->query("CREATE TABLE `posts` (
            `id` int(8) NOT NULL,
            `class` varchar(255) NOT NULL DEFAULT '\\HamletCMS\\BlogPosts\\Post',
            `title` varchar(255) NOT NULL,
            `summary` text,
            `content` text NOT NULL,
            `blog_id` bigint(10) NOT NULL,
            `link` varchar(150) NOT NULL,
            `link_override` tinyint(1) NOT NULL DEFAULT '0',
            `draft` tinyint(1) NOT NULL DEFAULT '0',
            `timestamp` datetime NOT NULL,
            `tags` varchar(300),
            `author_id` int(8) NOT NULL,
            `type` varchar(30) NOT NULL DEFAULT 'standard',
            `initialautosave` tinyint(1) NOT NULL DEFAULT '0',
            `teaser_image` varchar(100)
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        $dbc->query("CREATE TABLE `postviews` (
            `postid` int(8) NOT NULL,
            `userip` varchar(20) NOT NULL,
            `userviews` smallint(11) NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        $dbc->query("CREATE TABLE `postautosaves` (
            `post_id` int(11) NOT NULL,
            `content` text NOT NULL,
            `summary` text NOT NULL,
            `title` varchar(255) NOT NULL,
            `tags` varchar(255) NOT NULL,
            `date_last_saved` datetime NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        $dbc->query("ALTER TABLE `posts` ADD PRIMARY KEY (`id`), ADD KEY `title` (`title`);");
        $dbc->query("ALTER TABLE `posts` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
        $dbc->query("ALTER TABLE `postautosaves` ADD PRIMARY KEY (`post_id`);");
        $dbc->query("ALTER TABLE `postviews` ADD PRIMARY KEY (`postid`, `userip`);");
    }

    /**
     * Runs all test cases for the posts module
     */
    public function runUnitTests($args)
    {
        $context = $args['context'];

        if ($context === 'blog') {
            $blogID = $args['blogID'];

            // Create post
            $test = new tests\CreatePostTest();
            $test->blogID = $blogID;
            $test->run();

            // Clone post
            $cloneTest = new tests\ClonePostTest();
            $cloneTest->blogID = $blogID;
            $cloneTest->postToClone = $test->postID;
            $cloneTest->run();

            // Delete post
            $deleteTest = new tests\DeletePostTest();
            $deleteTest->blogID = $blogID;
            $deleteTest->postToDelete = $cloneTest->postID;
            $deleteTest->run();

            HamletCMS::runHook('runUnitTests', ['context' => 'post', 'blogID' => $blogID, 'post' => $test->postID]);
        }
    }

    /**
     * Adds a total post count to the blog dashboard
     */
    public function dashboardCounts($args)
    {
        $modelPostViews = HamletCMS::model('\HamletCMS\BlogPosts\model\PostViews');

        $args['counts']['posts'] = $this->model->countPostsOnBlog($args['blog']->id, true);
        $args['counts']['totalviews'] = $modelPostViews->getTotalPostViewsByBlog($args['blog']->id);
    }

}
