<?php
namespace rbwebdesigns\blogcms\Search\controller;

use rbwebdesigns\blogcms\GenericController;
use rbwebdesigns\blogcms\BlogCMS;

class Search extends GenericController
{
    public function __construct()
    {
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\Blog\model\Blogs');
        $this->modelPosts = BlogCMS::model('\rbwebdesigns\blogcms\BlogPosts\model\Posts');

        $this->request = BlogCMS::request();
        $this->response = BlogCMS::response();

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