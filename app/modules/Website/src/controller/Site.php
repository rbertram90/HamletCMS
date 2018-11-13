<?php
namespace rbwebdesigns\blogcms\Website\controller;

use rbwebdesigns\blogcms\BlogCMS;

class Site
{
    /**
     * @var \rbwebdesigns\blogcms\Blog\model\Blogs
     */
    protected $modelBlogs;
    /**
     * @var \rbwebdesigns\blogcms\BlogPosts\model\Posts
     */
    protected $modelPosts;

    protected $request, $response;

    public function __construct(&$request, &$response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\Blog\model\Blogs');
        $this->modelPosts = BlogCMS::model('\rbwebdesigns\blogcms\BlogPosts\model\Posts');
    }

    public function home()
    {
        $this->response->setTitle('Website homepage');
        $this->response->setDescription('Front page to your website powered by Blog CMS');
        $this->response->setVar('lettercounts', $this->modelBlogs->countBlogsByLetter());
        $this->response->setVar('categorycounts', $this->modelBlogs->countBlogsByCategory());
        $this->response->write('home.tpl', 'Website');
    }

}