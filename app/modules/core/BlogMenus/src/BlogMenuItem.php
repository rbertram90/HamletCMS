<?php

namespace HamletCMS\BlogMenus;

use HamletCMS\HamletCMS;

class BlogMenuItem {

    /** @var int */
    public $id;

    /** @var string */
    public $text;

    /** @var string */
    public $type;

    /** @var string */
    public $link_target;

    /** @var int */
    public $menu_id;

    /** @var int */
    public $weight;

    /** @var boolean */
    public $new_window;

    protected $menu = null;
    protected $url = null;

    public function menu() {
        if (is_null($this->menu)) {
            $menusModel = HamletCMS::model('\HamletCMS\BlogMenus\model\Menus');
            $this->menu = $menusModel->getMenuById($this->menu_id);
        }
        return $this->menu;
    }
    
    public function url()
    {
        if (is_null($this->url)) {
            $this->url = '';
            switch ($this->type) {
                case 'blog':
                    $blogModel = HamletCMS::model('\HamletCMS\Blog\model\Blogs');
                    if ($blog = $blogModel->getBlogById($this->link_target)) {
                        $this->url = $blog->relativePath();
                    }
                    else $this->url = '#';
                    break;
                case 'post':
                    $postModel = HamletCMS::model('\HamletCMS\BlogPosts\model\Posts');
                    if ($post = $postModel->getPostById($this->link_target)) {
                        $this->url = $post->relativePath();
                    }
                    else $this->url = '#';
                    break;
                case 'tag':
                    $blog = HamletCMS::getActiveBlog();
                    $this->url = $blog->relativePath() ."/tags/{$this->link_target}";
                    break;
                case 'mail':
                    $this->url = 'mailto:'. $this->link_target;
                    break;
                case 'tel':
                    $this->url = 'tel:'. $this->link_target;
                    break;
                case 'external':
                    $this->url = $this->link_target;
                    break;
            }
        }
        return $this->url;
    }
}