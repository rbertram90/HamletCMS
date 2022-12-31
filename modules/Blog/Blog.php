<?php

namespace HamletCMS\Blog;

use HamletCMS\HamletCMS;
use rbwebdesigns\core\JSONHelper;

class Blog
{
    public $id;
    public $name;
    public $description;
    public $domain;
    public $user_id;
    public $anon_search;
    public $visibility;
    public $category;
    public $logo;
    public $icon;

    protected $customFunctions = [];

    protected $contributors = null;

    /** @var \HamletCMS\Contributors\model\Contributors */
    protected $contributorsFactory = null;
    protected $posts = null;
    protected $postsFactory = null;
    protected $config = null;

    /**
     * Blog class constructor
     */
    public function __construct()
    {
        $this->contributorsFactory = HamletCMS::model('contributors');
        $this->postsFactory = HamletCMS::model('posts');

        // get callable functions from external modules
        HamletCMS::runHook('onBlogConstruct', ['blog' => $this, 'functions' => &$this->customFunctions]);
    }

    /**
     * Magic function for calling functions that have been added
     * to the post model by custom modules.
     */
    public function __call($closure, $args)
    {
        if (array_key_exists($closure, $this->customFunctions)) {
            array_unshift($args, $this);

            return call_user_func_array($this->customFunctions[$closure], $args);
        }
    }

    /**
     * Get the url path to append to blog resources (stylesheets, scripts)
     * 
     * @return string
     */
    public function relativePath()
    {
        return strlen($this->domain) ? "" : "/blogs/{$this->id}";
    }

    /**
     * Get the url to the front page of the blog
     * 
     * @return string
     */
    public function url()
    {
        return strlen($this->domain) ? $this->domain : "/blogs/{$this->id}";
    }

    /**
     * Get the path to blogdata folder
     */
    public function resourcePath()
    {
        return strlen($this->domain) ? "" : "/hamlet/blogdata/{$this->id}";
    }

    /**
     * Get all the users that can contribute to this blog
     * 
     * @return \HamletCMS\UserAccounts\User[]
     */
    public function contributors()
    {
        if ($this->contributors) {
            return $this->contributors;
        }
        $contributorIds = $this->contributorsFactory->getBlogContributors($this->id, true);
        $this->contributors = HamletCMS::model('useraccounts')->getByIds(array_column($contributorIds, 'user_id'));
        return $this->contributors;
    }

    /**
     * Is user a contributor to the blog
     * 
     * @param int $userID
     */
    public function isContributor($userID = 0)
    {
        if ($userID == 0) {
            $userID = HamletCMS::session()->currentUser['id'];
        }
        if (!$userID) {
            return false;
        }

        return $this->contributorsFactory->isBlogContributor($userID, $this->id);
    }

    /**
     * Get all the posts on this blog
     * 
     * @return \HamletCMS\BlogPosts\Post[]
     */
    public function posts()
    {
        if ($this->posts) {
            return $this->posts;
        }
        return $this->postsFactory->getPostsByBlog($this->id);
    }

    /**
     * Get the latest post on the blog
     * 
     * @return \HamletCMS\BlogPosts\Post|bool
     */
    public function latestPost()
    {
        $posts = $this->posts();
        return count($posts) > 0 ? $posts[0] : false;
    }

    /**
     * Get the config from JSON file
     * 
     * @return array
     */
    public function config()
    {
        $serverConfigPath = SERVER_PATH_BLOGS . "/{$this->id}/config.json";

        if ($this->config ?? false) {
            return $this->config;
        }
        elseif (file_exists($serverConfigPath)) {
            $this->config = JSONhelper::JSONFileToArray($serverConfigPath);
            return $this->config;
        }

        return [];
    }
    
    /**
     * Replaces the values in the config recursively
     * 
     * @param mixed[] $newConfig
     */
    public function updateConfig($newConfig)
    {
        if ($config = $this->config()) {
            $newConfig = array_replace_recursive($config, $newConfig);
        }

        $json = JSONhelper::arrayToJSON($newConfig);
        $save = file_put_contents(SERVER_PATH_BLOGS . "/{$this->id}/config.json", $json);
        return $save !== false;
    }

    /**
     * Hard overwrite of the config.json file for the blog (allows removal of things)
     * 
     * @todo maybe something more friendly to remove items?
     * 
     * @param mixed[] $newConfig
     */
    public function overwriteConfig($newConfig)
    {
        $json = JSONhelper::arrayToJSON($newConfig);
        $save = file_put_contents(SERVER_PATH_BLOGS . "/{$this->id}/config.json", $json);
        return $save !== false;
    }

}