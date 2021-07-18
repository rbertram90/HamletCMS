<?php

namespace HamletCMS\FavouriteBlogs\controller;

use HamletCMS\GenericController;
use HamletCMS\HamletCMS;

class Favourites extends GenericController
{

  /** @var \HamletCMS\Blog\Blog */
  protected $blog;

  /** @var \HamletCMS\FavouriteBlogs\model\FavouriteBlogs */
  protected $modelFavouriteBlogs;

  /** @var \HamletCMS\FavouriteBlogs\model\FavouritePosts */
  protected $modelFavouritePosts;

  protected $currentUserID;

  public function __construct()
  {
    parent::__construct();

    $this->blog = HamletCMS::getActiveBlog();
    $this->currentUserID = HamletCMS::session()->currentUser['id'];
    $this->modelFavouriteBlogs = HamletCMS::model('\HamletCMS\FavouriteBlogs\model\FavouriteBlogs');
    $this->modelFavouritePosts = HamletCMS::model('\HamletCMS\FavouriteBlogs\model\FavouritePosts');
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
