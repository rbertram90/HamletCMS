<?php

namespace rbwebdesigns\HamletCMS;

class FavouriteBlogs {

  public function onGenerateMenu($args) {
    $currentUser = HamletCMS::session()->currentUser['id'];

    if ($args['id'] == 'blog_actions' && $currentUser) {
      $link = new MenuLink();

      /** @var \rbwebdesigns\HamletCMS\FavouriteBlogs\model\FavouriteBlogs $favouriteBlogModel */
      $favouriteBlogModel = HamletCMS::model('\rbwebdesigns\HamletCMS\FavouriteBlogs\model\FavouriteBlogs');

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
    }
  }

  public function install($args) {
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

}
