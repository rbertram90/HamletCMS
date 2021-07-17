<?php

namespace HamletCMS\Search\controller;

use HamletCMS\GenericController;
use HamletCMS\HamletCMS;

class Search extends GenericController
{
    public function __construct()
    {
        parent::__construct();

        $this->modelBlogs = HamletCMS::model('\HamletCMS\Blog\model\Blogs');
        $this->modelPosts = HamletCMS::model('\HamletCMS\BlogPosts\model\Posts');

        $this->blog = $this->modelBlogs->getBlogById($this->request->getUrlParameter(0));
    }

    /**
     * View the search page which allows you to free text search blog posts for this blog
     */
    public function search()
    {
        $searchPhrase = $this->request->getString('q');
        $searchResults = [];

        // Perform Search
        if (strlen($searchPhrase) > 0) {
            $searchResults = $this->modelPosts->search($this->blog->id, $searchPhrase);
        }
        
        $this->response->setTitle('Search posts');
        $this->response->setVar('blog', $this->blog);
        $this->response->setVar('searchPhrase', $searchPhrase);
        $this->response->setVar('searchResults', $searchResults);
        $this->response->write('searchResults.tpl', 'Search');
    }
}