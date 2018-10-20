<?php

namespace rbwebdesigns\blogcms\LayoutPost\controller;

use rbwebdesigns\blogcms\BlogPosts\controller\AbstractPostType;

class LayoutPost extends AbstractPostType
{
    public function create()
    {
        parent::create();

        $this->response->setVar('imagesOutput', $this->getImagesOutput());
        $this->response->addScript('/js/layoutPost.js');
        $this->response->addStylesheet('/css/layoutPost.css');

        $this->response->write('layoutpost.tpl', 'LayoutPost');
    }

    protected function getImagesOutput()
    {
        $blog = $this->response->getVar('blog');
        $imagesHTML = '';
        $path = SERVER_ROOT ."/app/public/blogdata/{$blog['id']}/images";

        if (!is_dir($path)) {
            return;
        }
        if ($handle = opendir($path)) {
            while (false !== ($file = readdir($handle))) {
                $ext = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
                $filename = pathinfo($file, PATHINFO_FILENAME);
                
                if ($ext == 'JPG' || $ext == 'PNG' || $ext == 'GIF' || $ext == 'JPEG') {
                    $imagesHTML .= "<img src='/blogdata/{$blog['id']}/images/{$file}' height='100' data-name='{$filename}' class='selectableimage' />";
                }
            }
            closedir($handle);
        }
        
        return $imagesHTML;
    }

    public function edit()
    {
        parent::edit();

        $this->response->setVar('imagesOutput', $this->getImagesOutput());
        $this->response->addScript('/js/layoutPost.js');
        $this->response->addStylesheet('/css/layoutPost.css');
        $this->response->write('layoutpost.tpl', 'LayoutPost');
    }
}