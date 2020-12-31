<?php

namespace rbwebdesigns\HamletCMS\FavouriteBlogs\controller;

use rbwebdesigns\HamletCMS\GenericController;
use rbwebdesigns\HamletCMS\HamletCMS;

class Favourites extends GenericController
{

  /** @var \rbwebdesigns\HamletCMS\Blog\Blog */
  protected $blog;

  /** @var \rbwebdesigns\HamletCMS\FavouriteBlogs\model\FavouriteBlogs */
  protected $modelFavouriteBlogs;

  /** @var \rbwebdesigns\HamletCMS\FavouriteBlogs\model\FavouritePosts */
  protected $modelFavouritePosts;

  protected $currentUserID;

  public function __construct()
  {
    parent::__construct();

    $this->blog = HamletCMS::getActiveBlog();
    $this->currentUserID = HamletCMS::session()->currentUser['id'];
    $this->modelFavouriteBlogs = HamletCMS::model('\rbwebdesigns\HamletCMS\FavouriteBlogs\model\FavouriteBlogs');
    $this->modelFavouritePosts = HamletCMS::model('\rbwebdesigns\HamletCMS\FavouriteBlogs\model\FavouritePosts');
  }

  public function addPostToFavourites()
  {
    $post = $this->request->getInt('id', 0);
    if ($post) {
      $this->modelFavouritePosts->addFavourite($this->currentUserID, $post);
    }
    $this->response->redirect($this->blog->url());
  }

  public function removePostFromFavourites()
  {
    $post = $this->request->getInt('id', 0);
    if ($post) {
      $this->modelFavouritePosts->removeFavourite($this->currentUserID, $post);
    }
    $this->response->redirect($this->blog->url());
  }

  public function addBlogToFavourites() {
    $this->modelFavouriteBlogs->addFavourite($this->currentUserID, $this->blog->id);
    $this->response->redirect($this->blog->url());
  }

  public function removeBlogFromFavourites() {
    $this->modelFavouriteBlogs->removeFavourite($this->currentUserID, $this->blog->id);
    $this->response->redirect($this->blog->url());
  }

}
