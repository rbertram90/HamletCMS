<?php
namespace rbwebdesigns\blogcms\Website\controller;

use rbwebdesigns\blogcms\BlogCMS;

/**
 * Website controller
 * 
 * This class contains the functions that pull together the data and content
 * for pages on the front-end of the website.
 * 
 * For more information on adding new pages to the website see:
 * https://github.com/rbertram90/blog_cms/wiki/Creating-website-pages
 * 
 */
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
    /**
     * @var \rbwebdesigns\core\Request
     */
    protected $request;
    /**
     * @var \rbwebdesigns\blogcms\BlogCMSResponse
     */
    protected $response;

    /**
     * @param \rbwebdesigns\core\Request $request
     * @param \rbwebdesigns\blogcms\BlogCMSResponse $response
     */
    public function __construct(&$request, &$response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\Blog\model\Blogs');
        $this->modelPosts = BlogCMS::model('\rbwebdesigns\blogcms\BlogPosts\model\Posts');
    }

    /**
     * GET /
     */
    public function home()
    {
        // Set the page meta title
        $this->response->setTitle('Website homepage');
        // Set the page meta description
        $this->response->setDescription('Front page to your website powered by Blog CMS');

        // Get data from the blogs database
        $this->response->setVar('lettercounts', $this->modelBlogs->countBlogsByLetter());
        $this->response->setVar('categorycounts', $this->modelBlogs->countBlogsByCategory());

        // Output the homepage template
        $this->response->write('home.tpl', 'Website');
    }

}