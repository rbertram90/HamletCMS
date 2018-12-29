<?php

namespace rbwebdesigns\blogcms\BlogPosts;

use rbwebdesigns\blogcms\BlogCMS;

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

    /** @var rbwebdesigns\blogcms\BlogPosts\model\Posts $factory */
    protected $factory;

    /**
     * Post constructor
     * 
     * @param array $data
     *   Post properties as an array
     */
    public function __construct($data = [])
    {
        $this->factory = BlogCMS::model('\rbwebdesigns\blogcms\BlogPosts\model\Posts');

        foreach ($data as $key => $item) {
            $this->$key = $item;
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
        BlogCMS::runHook('onBeforePostSaved', ['post' => &$data]);
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
        BlogCMS::runHook('onBeforePostSaved', ['post' => &$data]);
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
}