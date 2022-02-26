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
        $this->modelFavouriteBlogs = HamletCMS::model('favouriteblogs');
        $this->modelFavouritePosts = HamletCMS::model('favouriteposts');
    }

    public function addPostToFavourites()
    {
        /** @var \HamletCMS\BlogPosts\model\Posts $modelPosts */
        $modelPosts = HamletCMS::model('\HamletCMS\BlogPosts\model\Posts');
        $postID = $this->request->getInt('id', 0);

        if ($post = $modelPosts->getPostById($postID)) {
            $this->modelFavouritePosts->addFavourite($this->currentUserID, $post->id);
            $this->response->redirect($post->url(), 'Favourite added', 'success');
        }
        $this->response->redirect($this->blog->url());
    }

    public function removePostFromFavourites()
    {
        /** @var \HamletCMS\BlogPosts\model\Posts $modelPosts */
        $modelPosts = HamletCMS::model('\HamletCMS\BlogPosts\model\Posts');
        $postID = $this->request->getInt('id', 0);

        if ($post = $modelPosts->getPostById($postID)) {
            $this->modelFavouritePosts->removeFavourite($this->currentUserID, $post->id);
            $this->response->redirect($post->url(), 'Favourite removed', 'success');
        }
        $this->response->redirect($this->blog->url());
    }

    public function addBlogToFavourites()
    {
        $insert = $this->modelFavouriteBlogs->addFavourite($this->currentUserID, $this->blog->id);
        [$message, $type] = $insert ? ['Favourite added', 'success'] : ['Unable to add favourite', 'error'];
        $this->response->redirect($this->blog->url(), $message, $type);
    }

    public function removeBlogFromFavourites()
    {
        $insert = $this->modelFavouriteBlogs->removeFavourite($this->currentUserID, $this->blog->id);
        [$message, $type] = $insert ? ['Favourite removed', 'success'] : ['Unable to remove favourite', 'error'];
        $this->response->redirect($this->blog->url(), $message, $type);
    }

    public function viewFavourites()
    {
        $this->response->setTitle('My Favourites');
        $this->response->setVar('favouriteBlogs', $this->modelFavouriteBlogs->getAllFavourites($this->currentUserID));
        $this->response->setVar('favouritePosts', $this->modelFavouritePosts->getAllFavourites($this->currentUserID));
        $this->response->write('view.tpl', 'FavouriteBlogs');
    }

}
