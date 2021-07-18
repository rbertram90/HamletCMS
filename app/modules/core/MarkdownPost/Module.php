<?php
namespace HamletCMS\MarkdownPost;

use Michelf\Markdown;

class Module
{
    public function onViewEditPost($args) {
        if ($args['type'] != 'standard') return;

        $controller = new controller\MarkdownPost();
        $controller->edit();
    }

    public function runTemplate($args)
    {
        $post = $args['post'];
        if ($post->type !== 'standard') return;

        switch ($args['template']) { 
            case 'singlePost':
            case 'postTeaser':
                $args['post']->summary = Markdown::defaultTransform($post->summary);
                $args['post']->content = Markdown::defaultTransform($post->content);
                break;
        }
    }

}