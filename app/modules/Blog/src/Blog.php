<?php

namespace rbwebdesigns\blogcms\Blog;

use rbwebdesigns\blogcms\BlogCMS;
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

    protected $contributors = null;
    protected $contributorsFactory = null;
    protected $posts = null;
    protected $postsFactory = null;

    /**
     * Blog class constructor
     */
    public function __construct()
    {
        $this->contributorsFactory = BlogCMS::model('\rbwebdesigns\blogcms\Contributors\model\Contributors');
        $this->postsFactory = BlogCMS::model('\rbwebdesigns\blogcms\BlogPosts\model\Posts');
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
        return strlen($this->domain) ? "" : "/blogdata/{$this->id}";
    }

    /**
     * Get all the users that can contribute to this blog
     * 
     * @return \rbwebdesigns\blogcms\User[]
     */
    public function contributors()
    {
        if ($this->contributors) {
            return $this->contributors;
        }
        return $this->contributorsFactory->getBlogContributors($this->id);
    }

    /**
     * Is user a contributor to the blog
     * 
     * @param int $userID
     */
    public function isContributor($userID = 0)
    {
        if ($userID == 0) {
            $userID = BlogCMS::session()->currentUser['id'];
        }
        return $this->contributorsFactory->isBlogContributor($userID, $this->id);
    }

    /**
     * Get all the posts on this blog
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
     * @return rbwebdesigns\blogcms\BlogPosts\Post|bool
     */
    public function latestPost()
    {
        $posts = $this->posts();
        return count($posts) > 0 ? $posts[0] : false;
    }

    /**
     * Get the config from JSON file
     */
    public function config()
    {
        $serverConfigPath = SERVER_PUBLIC_PATH ."/blogdata/{$this->id}/config.json";

        if ($this->config) {
            return $this->config;
        }
        elseif (file_exists($serverConfigPath)) {
            $this->config = JSONhelper::JSONFileToArray($serverConfigPath);
            return $this->config;
        }
        return null;
    }
    
    /**
     * Change the config
     * 
     * @param mixed[] $newConfig
     */
    public function updateConfig($newConfig)
    {
        if ($config = $this->config()) {
            $newConfig = array_replace_recursive($config, $newConfig);
        }

        $json = JSONhelper::arrayToJSON($newConfig);
        $save = file_put_contents(SERVER_PUBLIC_PATH . "/blogdata/{$this->id}/config.json", $json);
        return $save !== false;
    }

}