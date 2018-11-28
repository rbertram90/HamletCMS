<?php

namespace rbwebdesigns\blogcms\Blog;

class Blog
{
    public $id;
    public $name;
    public $description;
    public $domain;
    public $user_id;
    public $anon_search;
    public $visibility;
    public $widgetJSON;
    public $pagelist;
    public $category;

    public function reletivePath()
    {
        return strlen($this->domain) ? "" : "/blogs/{$this->id}";
    }
}