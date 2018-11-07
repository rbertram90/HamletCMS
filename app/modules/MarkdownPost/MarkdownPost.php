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

        if ($args['template'] == 'postTeaser' && $post['type'] == 'standard') {
            $content = Markdown::defaultTransform($post['content']);
            $args['post']['trimmedContent'] = $content; // $this->trimContent($content, $summarylength);
        }
    }

}