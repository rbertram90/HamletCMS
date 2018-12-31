<?php
namespace rbwebdesigns\blogcms;

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
                $content = Markdown::defaultTransform($post->content);
                $args['post']->trimmedContent = $content;
                break;
            case 'postTeaser':
                $args['post']->trimmedContent = $post->summary;
                break;
        }
    }

}