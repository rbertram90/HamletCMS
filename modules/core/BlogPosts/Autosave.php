<?php

namespace HamletCMS\BlogPosts;

use HamletCMS\HamletCMS;

class Autosave
{
    /** Core post attributes (match database columns) */
    /** @var int $id */
    public $post_id = 0;
    /** @var string $title */
    public $title;
    /** @var string $summary */
    public $summary;
    /** @var string $content */
    public $content;
    /** @var string $tags */
    public $tags;
    /** @var string $date_last_saved */
    public $date_last_saved;

    /** @var HamletCMS\BlogPosts\model\Posts $factory */
    protected $factory;

    /**
     * Post constructor
     * 
     * @param array $data
     *   Post properties as an array
     */
    public function __construct($data = [])
    {
        $this->factory = HamletCMS::model('\HamletCMS\BlogPosts\model\Autosaves');

        foreach ($data as $key => $item) {
            $this->$key = $item;
        }
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