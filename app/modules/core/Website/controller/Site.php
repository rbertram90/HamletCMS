<?php
namespace HamletCMS\Website\controller;

use HamletCMS\HamletCMS;

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
     * @var \HamletCMS\Blog\model\Blogs
     */
    protected $modelBlogs;
    /**
     * @var \HamletCMS\BlogPosts\model\Posts
     */
    protected $modelPosts;
    /**
     * @var \rbwebdesigns\core\Request
     */
    protected $request;
    /**
     * @var \HamletCMS\HamletCMSResponse
     */
    protected $response;

    /**
     * @param \rbwebdesigns\core\Request $request
     * @param \HamletCMS\HamletCMSResponse $response
     */
    public function __construct(&$request, &$response)
    {
        $this->request = $request;
        $this->response = $response;
        $this->modelBlogs = HamletCMS::model('\HamletCMS\Blog\model\Blogs');
        $this->modelPosts = HamletCMS::model('\HamletCMS\BlogPosts\model\Posts');
    }

    /**
     * GET /
     */
    public function home()
    {
        // Set the page meta title
        $this->response->setTitle('Website homepage');
        // Set the page meta description
        $this->response->setDescription('Front page to your website powered by HamletCMS');

        // Get data from the blogs database
        $this->response->setVar('lettercounts', $this->modelBlogs->countBlogsByLetter());
        $this->response->setVar('categorycounts', $this->modelBlogs->countBlogsByCategory());

        // Output the homepage template
        $this->response->write('home.tpl', 'Website');
    }

}