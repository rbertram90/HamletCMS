<?php
namespace HamletCMS\API\controller;

use HamletCMS\Blog\model\Blogs;
use HamletCMS\BlogPosts\model\Posts;
use HamletCMS\Contributors\model\Contributors;
use rbwebdesigns\core\JSONhelper;
use HamletCMS\GenericController;
use HamletCMS\HamletCMS;
use HamletCMS\UserAccounts\model\UserAccounts;
use rbwebdesigns\core\Request;
use rbwebdesigns\core\Response;

/**
 * ApiController
 * 
 * @author R Bertram <ricky@rbwebdesigns.co.uk>
 * 
 * @todo decide on where data is coming from for cms vs public facing api
 * @todo cross origin domain restrictions
 */
class Api extends GenericController
{
    protected Blogs $modelBlogs;
    protected Posts $modelPosts;
    protected UserAccounts $modelUsers;
    protected Contributors $modelContributors;
    protected Request $request;
    protected Response $response;
    
    public function __construct()
    {
        $this->modelBlogs = HamletCMS::model('blogs');
        $this->modelPosts = HamletCMS::model('posts');
        $this->modelUsers = HamletCMS::model('useraccounts');
        $this->modelContributors = HamletCMS::model('contributors');

        parent::__construct();
    }
    
    /**
     * Handles /api
     * Currently nothing implemented - would be helpful to redirect to some
     * sort of documentation page
     */
    public function defaultAction()
    {
        $result = [
            'error' => 'Unknown API method'
        ];
        $this->response->addHeader('Content-Type', 'application/json');
        $this->response->code(404);
        $this->response->setBody(JSONhelper::arrayToJSON($result));
        $this->response->writeBody();
    }

    /**
     * GET /api/tags
     */
    public function tags()
    {
        $blogID = $this->request->getInt('blogID', -1);

        $sort = $this->request->getString('sort', 'text'); // text or count

        if (!$blog = $this->modelBlogs->getBlogById($blogID)) {
            die("{ 'error': 'Unable to find blog' }");
        }

        $tags = $this->modelPosts->countAllTagsByBlog($blog->id, $sort);

        if (defined('CUSTOM_DOMAIN') && CUSTOM_DOMAIN) {
            $this->response->addHeader('Access-Control-Allow-Origin', $blog->domain);
        }
        $this->response->setBody(JSONhelper::arrayToJSON($tags));
    }

    /**
     * GET /api/blogs
     * GET /api/blogs/count
     */
    public function blogs()
    {
        if ($blogID = $this->request->getInt('blogID', false)) {
            if(!$blog = $this->modelBlogs->getBlogById($blogID)) {
                die("{ 'error': 'Unable to find blog' }");
            }
        }

        switch ($this->request->getUrlParameter(0)) {
            case 'byLetter':
                $this->blogsByLetter();
                break;
            case 'byCategory':
                $this->blogsByCategory();
                break;
            case 'count':
                $this->blogsCount();
                break;
            default:
                $this->blogsById($blog);
                break;
        }
    }

    /**
     * GET /api/contributors
     * GET /api/contributors/owner
     */
    public function contributors()
    {
        $blogID = $this->request->getInt('blogID', false);

        if (!$blog = $this->modelBlogs->getBlogById($blogID)) {
            die("{ 'error': 'Unable to find blog' }");
        }

        switch ($this->request->getUrlParameter(0)) {
            case 'owner':
                $this->blogOwner($blog);
                break;
            default:
                $this->blogContributors($blog);
                break;
        }
    }

    /**
     * GET /api/contributors
     */
    protected function blogContributors($blog)
    {
        $contributors = $this->modelContributors->getBlogContributors($blog->id);

        // Remove potentially sensitive data
        for ($i = 0; $i < count($contributors); $i++) {
            unset($contributors[$i]->password);
            unset($contributors[$i]->security_q);
            unset($contributors[$i]->security_a);
        }

        $this->response->setBody(JSONhelper::arrayToJSON($contributors));
    }

    /**
     * GET /api/contributors/owner
     */
    protected function blogOwner($blog)
    {
        $owner = $this->modelUsers->getById($blog->user_id);

        unset($owner->password);
        unset($owner->security_q);
        unset($owner->security_a);

        $this->response->setBody(JSONhelper::arrayToJSON($owner));
    }

    /**
     * GET /api/blogs
     * 
     * $_GET Parameters:
     * blogID
     */
    protected function blogsById($blog)
    {
        $this->response->setBody(JSONhelper::arrayToJSON($blog));
    }
    
    /**
     * GET /api/blogs/count
     * GET /api/blogs/count/byLetter
     * 
     * $_GET Parameters:
     * letter optional
     */
    protected function blogsCount()
    {
        $method = $this->request->getUrlParameter(1);

        if ($method == 'byLetter') {
            $counts = $this->modelBlogs->countBlogsByLetter();
            $this->response->setBody(JSONhelper::arrayToJSON($counts));
            return;
        }

        $blogCount = $this->modelBlogs->count();
        $this->response->setBody(JSONhelper::arrayToJSON($blogCount));
    }

    /**
     * GET /api/blogs/byLetter?letter=<letter>
     */
    protected function blogsByLetter()
    {
        $letter = $this->request->getString('letter', false);

        if ($letter) {
            $blogs = $this->modelBlogs->getBlogsByLetter($letter);
        }
        else {
            $blogs = [];
        }

        $this->response->setBody(JSONhelper::arrayToJSON($blogs));
    }

    /**
     * GET /api/blogs/byCategory?category=<category>
     */
    protected function blogsByCategory()
    {
        $category = $this->request->getString('category', false);

        if ($category) {
            $blogs = $this->modelBlogs->get(['name', 'id', 'description'], ['category' => $category]);
        }
        else {
            $blogs = [];
        }

        $this->response->setBody(JSONhelper::arrayToJSON($blogs));
    }
    
}
