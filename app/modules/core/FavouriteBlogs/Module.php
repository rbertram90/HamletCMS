<?php

namespace HamletCMS\FavouriteBlogs;

use HamletCMS\HamletCMS;
use HamletCMS\MenuLink;

class Module
{

    public function onGenerateMenu($args)
    {
        $currentUser = HamletCMS::session()->currentUser['id'];

        if ($args['id'] == 'blog_actions' && $currentUser) {
            $link = new MenuLink();

            /** @var \HamletCMS\FavouriteBlogs\model\FavouriteBlogs $favouriteBlogModel */
            $favouriteBlogModel = HamletCMS::model('\HamletCMS\FavouriteBlogs\model\FavouriteBlogs');

            if ($favouriteBlogModel->isFavourite($currentUser, $args['blog']->id)) {
                $link->url = HamletCMS::route('favourites.remove.blog', [
                    'BLOG_ID' => $args['blog']->id
                ]);
                $link->text = 'Remove blog from favourites';
            }
            else {
                $link->url = HamletCMS::route('favourites.add.blog', [
                    'BLOG_ID' => $args['blog']->id
                ]);
                $link->text = 'Add blog to favourites';
            }

            $args['menu']->addLink($link);

            if (isset($args['post'])) {
                $link = new MenuLink();
                
                /** @var \HamletCMS\FavouriteBlogs\model\FavouritePosts $favouritePostModel */
                $favouritePostModel = HamletCMS::model('\HamletCMS\FavouriteBlogs\model\FavouritePosts');

                if ($favouritePostModel->isFavourite($currentUser, $args['post']->id)) {
                    $link->url = HamletCMS::route('favourites.remove.post', [
                        'BLOG_ID' => $args['blog']->id
                    ]);
                    $link->url .= '?id=' . $args['post']->id;
                    $link->text = 'Remove post from favourites';
                }
                else {
                    $link->url = HamletCMS::route('favourites.add.post', [
                        'BLOG_ID' => $args['blog']->id
                    ]);
                    $link->url .= '?id=' . $args['post']->id;
                    $link->text = 'Add post to favourites';
                }

                $args['menu']->addLink($link);
            }            
        }
    }

    public function install()
    {
        $dbc = HamletCMS::databaseConnection();

        $dbc->query("CREATE TABLE `favouriteposts` (
            `post_id` int(11) NOT NULL,
            `user_id` int(11) NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        $dbc->query("CREATE TABLE `favouriteblogs` (
            `blog_id` bigint(11) NOT NULL,
            `user_id` int(11) NOT NULL
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        $dbc->query("ALTER TABLE `favouriteposts` ADD PRIMARY KEY (`post_id`,`user_id`);");
        $dbc->query("ALTER TABLE `favouriteblogs` ADD PRIMARY KEY (`user_id`,`blog_id`);");
    }

    public function uninstall()
    {
        $dbc = HamletCMS::databaseConnection();
        $dbc->query("DROP TABLE IF EXISTS `favouriteposts`;");
        $dbc->query("DROP TABLE IF EXISTS `favouriteblogs`;");
    }
}
