<?php

namespace rbwebdesigns\blogcms\BlogPosts;

use rbwebdesigns\blogcms\BlogCMS;

class Post
{
    /** Core post attributes (match database columns) */
    /** @var int $id */
    public $id;
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
     */
    public function __construct()
    {
        $this->factory = BlogCMS::model('\rbwebdesigns\blogcms\BlogPosts\model\Posts');
    }
}