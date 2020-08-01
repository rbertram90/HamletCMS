<?php

namespace rbwebdesigns\HamletCMS\BlogPosts;

use rbwebdesigns\HamletCMS\HamletCMS;

class Post
{
    /** Core post attributes (match database columns) */
    /** @var int $id */
    public $id = 0;
    /** @var string $title */
    public $title;
    /** @var string $summary */
    public $summary;
    /** @var string $content */
    public $content;
    /** @var int $blog_id */
    public $blog_id;
    /** @var string $link */
    public $link;
    /** @var boolean $draft */
    public $draft;
    /** @var string $timestamp */
    public $timestamp;
    /** @var string $tags */
    public $tags;
    /** @var int $author_id */
    public $author_id;
    /** @var string $type */
    public $type;
    /** @var boolean $initialautosave */
    public $initialautosave;
    /** @var string $teaser_image */
    public $teaser_image;

    protected $customFunctions = [];

    /** @var \rbwebdesigns\HamletCMS\UserAccounts\User|null */
    protected $author = null;

    /** @var \rbwebdesigns\HamletCMS\Blog\Blog|null */
    protected $blog = null;

    /** @var \rbwebdesigns\HamletCMS\BlogPosts\model\Posts */
    protected $factory;

    /**
     * Post constructor
     * 
     * @param array $data
     *   Post properties as an array
     */
    public function __construct($data = [])
    {
        $this->factory = HamletCMS::model('\rbwebdesigns\HamletCMS\BlogPosts\model\Posts');

        foreach ($data as $key => $item) {
            $this->$key = $item;
        }

        // get callable functions from external modules
        HamletCMS::runHook('onPostConstruct', ['post' => $this, 'functions' => &$this->customFunctions]);
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
     * Get a list of tags an an array
     * 
     * @return array
     */
    public function tags() {
        return explode(',', $this->tags);
    }

    /**
     * Save the post to the database
     * 
     * @return bool
     *   Was save successful?
     */
    public function save()
    {
        return $this->id > 0 ? $this->update() : $this->create();
    }

    /**
     * Update a record in database for this post
     * protected from public view - should use save method
     */
    protected function update()
    {
        $data = $this->toArray();
        HamletCMS::runHook('onBeforePostSaved', ['post' => &$data]);
        unset($data['id']);
        unset($data['type']); // type cannot be changed
        return $this->factory->update(['id' => $this->id], $data);
    }

    /**
     * Create a new record in database for this post
     * protected from public view - should use save method
     */
    protected function create()
    {
        $data = $this->toArray();
        HamletCMS::runHook('onBeforePostSaved', ['post' => &$data]);
        unset($data['id']);
        return $this->factory->insert($data);
    }

    /**
     * Get an array of class properties
     * 
     * @return array
     */
    public function toArray()
    {
        $fields = $this->factory->getFields();
        foreach ($fields as $key => $field) {
            $fields[$field] = $this->$key;
        }
        return $fields;
    }

    /**
     * Get the user record for a post
     * 
     * @return \rbwebdesigns\HamletCMS\UserAccounts\User
     */
    public function author()
    {
        if (is_null($this->author)) {
            /** @var \rbwebdesigns\HamletCMS\UserAccounts\model\UserAccounts $usersModel */
            $usersModel = HamletCMS::model('\rbwebdesigns\HamletCMS\UserAccounts\model\UserAccounts');
            $this->author = $usersModel->getById($this->author_id);
        }
        return $this->author;
    }

    /**
     * Get the blog record for this post
     * 
     * @return \rbwebdesigns\HamletCMS\Blog\Blog
     */
    public function blog()
    {
        if (is_null($this->blog)) {
            $blogsModel = HamletCMS::model('\rbwebdesigns\HamletCMS\Blog\model\Blogs');
            $this->blog = $blogsModel->getBlogById($this->blog_id);
        }
        return $this->blog;
    }

    /**
     * Get the URL path for this post
     * 
     * @return string
     */
    public function relativePath()
    {
        return "{$this->blog()->relativePath()}/posts/{$this->link}";
    }

    /**
     * Get the absolute URL for this post
     * 
     * @return string
     */
    public function url()
    {
        return "{$this->blog()->url()}/posts/{$this->link}";
    }

    /**
     * Is the post visible to anonymous users?
     * 
     * @return bool
     */
    public function isPublic()
    {
        return $this->draft == 0 && strtotime($this->timestamp) <= time();
    }

    /**
     * Get the previous post in chronological order
     * 
     * @return \rbwebdesigns\HamletCMS\BlogPosts\Post|null
     */
    public function previous()
    {
        return $this->factory->getPreviousPost($this->blog_id, $this->timestamp);
    }

    /**
     * Get the next post in chronological order
     * 
     * @return \rbwebdesigns\HamletCMS\BlogPosts\Post|null
     */
    public function next()
    {
        return $this->factory->getNextPost($this->blog_id, $this->timestamp);
    }
    
    /**
     * Determine if the post is not yet visible on the blog
     */
    public function isScheduled()
    {
        return $this->timestamp > date('Y-m-d H:i:s');
    }
    
}
