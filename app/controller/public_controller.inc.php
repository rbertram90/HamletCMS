<?php
namespace rbwebdesigns\blogcms;

class PublicController
{
    /**
     * @var \rbwebdesigns\blogcms\model\Blogs
     */
    protected $modelBlogs;
    /**
     * @var \rbwebdesigns\blogcms\model\Blogs
     */
    protected $modelPosts;

    protected $request, $response;

    public function __construct(&$request, &$response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\model\Blogs');
        $this->modelPosts = BlogCMS::model('\rbwebdesigns\blogcms\model\Posts');
    }

    public function home()
    {
        $this->response->setTitle('Website homepage');
        $this->response->setDescription('Front page to your website powered by Blog CMS');
        $this->response->setVar('lettercounts', $this->modelBlogs->countBlogsByLetter());
        $this->response->write('public/home.tpl');
    }

}