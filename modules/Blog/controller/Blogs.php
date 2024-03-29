<?php

namespace HamletCMS\Blog\controller;

use HamletCMS\GenericController;
use HamletCMS\HamletCMS;
use HamletCMS\Menu;
use rbwebdesigns\core\JSONhelper;

/**
 * controllers/Blogs
 * 
 * Functions in this controller are called via. the routing system. Each
 * function fetches all the resources that is required by the view.
 * 
 * @author Ricky Bertram <ricky@rbwebdesigns.co.uk>
 */
class Blogs extends GenericController
{
    /**
     * Route: /cms/blog
     *
     * View the cms main dashboard that shows all blogs that the user
     * contributes to.
     */
    public function home()
    {
        $user = HamletCMS::session()->currentUser;
        $blogs = $this->model('contributors')->getContributedBlogs($user['id']);
        
        // Allow other modules to add their own links
        foreach ($blogs as $key => $blog) {
            $blogActions = new Menu('bloglist');
            HamletCMS::runHook('onGenerateMenu', [
                'id' => 'bloglist',
                'menu' => &$blogActions,
                'blog' => $blog
            ]);
            $blogs[$key]->actions = $blogActions->getLinks();
        }
        
        HamletCMS::$activeMenuLink = '/cms/blog';

        // Add to template
        $this->response->setVar('blogs', $blogs);
        $this->response->addScript('/hamlet/js/showUserCard.js');
        $this->response->setTitle('My Blogs');
        $this->response->write('index.tpl', 'Blog');
    }
    
    /**
     * Route: /cms/blog/create
     *
     * View the new blog form
     */
    public function create()
    {
        if ($this->request->method() === 'POST') return $this->runCreateBlog();

        $this->response->setTitle('Create New Blog');
        $this->response->write('newblog.tpl', 'Blog');
    }
        
    /**
     * View overview/ summary of a single blog
     */
    public function overview()
    {
        $blogID = $this->request->getUrlParameter(1);
        $blog = $this->model('blogs')->getBlogById($blogID);

        // Validation
        if (!$blog) {
            $this->response->redirect('/cms');
        }
        elseif (!$blog->isContributor()) {
            $this->response->redirect('/cms', 'You do not contribute to this blog', 'error');
        }

        // Get top level statistics for dashboard
        $counts = [];
        HamletCMS::runHook('dashboardCounts', ['blog' => $blog, 'counts' => &$counts]);
        $this->response->setVar('counts', $counts);
        
        $panels = [];
        HamletCMS::runHook('dashboardPanels', ['blog' => $blog, 'panels' => &$panels]);
        $this->response->setVar('panels', $panels);
        
        $this->response->setVar('blog', $blog);
        $this->response->setVar('posts', $this->model('posts')->getPostsByBlog($blogID, 1, 5, 1, 1));
        
        if (HamletCMS::getModule('EventLogger')) {
            $events = $this->model('eventlogger')->byBlog($blogID);
            $userIDs = array_unique(array_column($events, 'user_id'));
            $users = $this->model('useraccounts')->getByIds($userIDs);
            $users_keyed = [];
            foreach ($users as $user) $users_keyed[$user->id] = $user;

            foreach ($events as &$event) {
                $event['user'] = $users_keyed[$event['user_id']];
            }

            $this->response->setVar('activitylog', $events);
        }

        $this->response->setBreadcrumbs([
            $blog->name => null
        ]);
        $this->response->headerIcon = 'book';
        $this->response->headerText = $blog->name;

        HamletCMS::$activeMenuLink = '/cms/blog/overview/'. $blog->id;
        $this->response->setTitle('Dashboard - '. $blog->name);
        $this->response->write('overview.tpl', 'Blog');
    }
    
    /**
     * Create a new blog
     */
    public function runCreateBlog()
    {
        $currentUser = HamletCMS::session()->currentUser;

        // Check we've got the root.inc.php file under blogdata root
        if (!file_exists(SERVER_PATH_BLOGS . '/root.inc.php')) {
            // Replace contents
            $fileContents = file_get_contents(SERVER_ROOT.'/app/root.default.php');

            $fileContents = str_replace("{SERVER_ROOT}", HamletCMS::config()['environment']['root_directory'], $fileContents);
            $fileContents = str_replace("{CMS_DOMAIN}", HamletCMS::config()['environment']['canonical_domain'], $fileContents);

            // Copy file
            $copy = file_put_contents(SERVER_PATH_BLOGS . '/root.inc.php', $fileContents);
            if (!$copy) die("Failed to create root file, please check directory permissions for: " . SERVER_PATH_BLOGS);
        }

        $config = HamletCMS::config();
        $limit = 999;
        if (isset($config['general']) && isset($config['general']['maxUserBlogLimit'])) {
            $limit = $config['general']['maxUserBlogLimit'];
        }
        if ($this->model('blogs')->countBlogsByUser($currentUser['id']) > $limit) {
            $this->response->redirect('/cms', 'Unable to continue - maximum number of blogs exceeded!', 'Error');
        }
        
        // Create blog db entry
        $newblogkey = $this->model('blogs')->createBlog($this->request->getString('fld_blogname'), $this->request->getString('fld_blogdesc'));

        if (!$newblogkey) {
            $this->response->redirect('/cms', 'Error creating blog please try again later', 'error');
        }

        // Create admin groups
        // @todo get this function to return the admin group ID!
        if (!$this->model('contributorgroups')->createDefaultGroups($newblogkey)) {
            $this->response->redirect('/cms', 'Error creating contributor groups please try again later', 'error');
        }

        $adminGroup = $this->model('contributorgroups')->get(['id'], ['blog_id' => $newblogkey, 'name' => 'Admin'], '', '', false);
        if (!$adminGroup) die('No admin group found ' . $newblogkey);

        // Add the user as contributor
        if (!$this->model('contributors')->addBlogContributor($currentUser['id'], $newblogkey, $adminGroup->id)) {
            $this->response->redirect('/cms', 'Error adding to contributor please try again later', 'error');
        }

        $this->response->redirect('/cms/blog/overview/'. $newblogkey, 'Blog created', 'success');
    }

    /**
     * Confirm delete page
     */
    public function delete()
    {
        $currentUser = HamletCMS::session()->currentUser;
        $blogID = $this->request->getUrlParameter(1);

        if (!$blog = $this->model('blogs')->getBlogById($blogID)) {
            $this->response->redirect('/cms', 'Blog not found', 'error');
        }

        // Only the owner can delete the blog
        if ($blog->user_id != $currentUser['id']) {
            $this->response->redirect('/cms', 'Access denied', 'error');
        }

        if ($this->request->method() == 'POST') return $this->runDeleteBlog($blog);

        $this->response->setTitle('Delete blog');
        $this->response->write('deleteblog.tpl', 'Blog');
    }

    /**
     * Action the delete blog
     * 
     * @todo postviews not being deleted
     */
    protected function runDeleteBlog($blog)
    {
        HamletCMS::runHook('onDeleteBlog', ['blog' => $blog]);

        // Delete posts
        $this->model('contributors')->delete(['blog_id' => $blog->id]);
        $this->model('contributorgroups')->delete(['blog_id' => $blog->id]);
        $this->model('posts')->delete(['blog_id' => $blog->id]);
        $this->model('blogs')->delete(['id' => $blog->id]);

        try {
            $this->deleteDir(SERVER_PATH_BLOGS . '/' . $blog->id);
        }
        catch (\InvalidArgumentException $e) {
            // It doesn't really matter if the files still
            // exists in the file system - would be nice to notify
            // the system administrator though...
            if (IS_DEVELOPMENT) {
                die($e->getMessage());
            }
        }

        $this->response->redirect('/cms/blog', 'Blog deleted', 'success');
    }

    /**
     * @todo This could be moved into a core function
     * Source: https://stackoverflow.com/a/3349792
     */
    protected function deleteDir($dirPath)
    {
        if (!is_dir($dirPath)) {
            throw new \InvalidArgumentException("$dirPath must be a directory");
        }
        if (substr($dirPath, strlen($dirPath) - 1, 1) != '/') {
            $dirPath .= '/';
        }

        $files = scandir($dirPath);
        foreach ($files as $file) {
            if ($file == "." || $file == "..") {
                continue;
            }
            elseif (is_dir($dirPath.$file)) {
                self::deleteDir($dirPath.$file);
            } else {
                unlink($dirPath.$file);
            }
        }
        rmdir($dirPath);
    }

    /**
     * Free text search for a blog
     */
    public function search()
    {
        $search = $this->request->getString('q', false);

        if (!$search || strlen($search) == 0) {
            $results = [];
        }
        else {
            $results = $this->model('blogs')->search($search);
        }

        $data = [];

        foreach ($results as $result) {
            $data[] = [
                'name' => $result->name,
                'value' => $result->id,
            ];
        }

        $this->response->setBody(JSONhelper::arrayToJSON(['success' => true,
            'results' => $data
        ]));
    }

}
