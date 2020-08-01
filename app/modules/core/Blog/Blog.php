<?php

namespace rbwebdesigns\HamletCMS;

use rbwebdesigns\HamletCMS\Link;

class Blog
{

    public function onGenerateMenu($options)
    {
        if ($options['id'] == 'cms_main_actions') {
            $newLinks = [];
            $currentLinks = $options['menu']->getLinks();
            foreach ($currentLinks as $link) {
                // Check if the BLOG_ID token has been replaced - if not
                // remove the link
                if ($link->url) {
                    if (strpos($link->url, '{BLOG_ID}') === false) {
                        $newLinks[] = $link;
                    }
                }
                elseif (!HamletCMS::$blogID && strtolower($link->text) == 'blog actions') {
                    // Yes this is not clean - difficult scenario...
                    // don't want to show the "blog actions" label when no blog is selected
                    // @todo maybe add a new key to menu link for dependency?
                }
                else {
                    $newLinks[] = $link;
                }
            }
            if ($blog = HamletCMS::getActiveBlog()) {
                $viewBlogLink = new MenuLink();
                $viewBlogLink->text = 'View blog';
                $viewBlogLink->url = $blog->url();
                $viewBlogLink->icon = 'book';
                $newLinks[] = $viewBlogLink;
            }
            $options['menu']->setLinks($newLinks);
        }
    }

    /**
     * Run database setup
     */
    public function install()
    {
        $dbc = HamletCMS::databaseConnection();

        $dbc->query("CREATE TABLE `blogs` (
            `id` bigint(10) NOT NULL,
            `name` varchar(150) NOT NULL,
            `domain` varchar(150),
            `description` text,
            `icon` varchar(25),
            `logo` varchar(25),
            `user_id` int(8) NOT NULL,
            `anon_search` tinyint(1) NOT NULL DEFAULT '1',
            `visibility` enum('anon','private','members','friends') NOT NULL DEFAULT 'anon',
            `category` varchar(50) NOT NULL DEFAULT 'general'
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        $dbc->query("ALTER TABLE `blogs` ADD PRIMARY KEY (`id`);");
    }

    public function runUnitTests($args)
    {
        $context = $args['context'];

        if ($context === 'root') {

            $createBlogTest = new blog\tests\CreateBlogTest();
            $createBlogTest->run();
        
            // Run every other test!
            HamletCMS::runHook('runUnitTests', ['context' => 'blog', 'blogID' => $createBlogTest->blogID]);
        }

        // Required tests
        // Overview?
        // Delete Blog - note - this needs to run after everything else...

        // $test = new \rbwebdesigns\HamletCMS\Blog\tests\();
        // $test->blogID = $blogID;
        // $test->run();
    }

}
