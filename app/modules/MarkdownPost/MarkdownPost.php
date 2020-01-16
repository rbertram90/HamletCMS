<?php
namespace rbwebdesigns\HamletCMS;

use Michelf\Markdown;

class MarkdownPost
{
    public function onViewEditPost($args) {
        if ($args['type'] != 'standard') return;

        $controller = new MarkdownPost\controller\MarkdownPost();
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