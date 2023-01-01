<?php
namespace HamletCMS\VideoPost;

use Michelf\Markdown;
use HamletCMS\HamletCMS;

class Module
{
    public function onViewEditPost($args) {
        if ($args['type'] != 'video') return;

        $controller = new controller\VideoPost();
        $controller->edit();
    }

    public function runTemplate($args)
    {
        $post = $args['post'];
        if ($post->type !== 'video') return;

        $videoContent = '<div class="ui embed video-embed-' . $post->id . '" data-id="'. $post->videoid .'" data-source="' . $post->videosource . '"></div><script>$(\'.video-embed-' . $post->id . '\').embed();</script>';

        $args['post']->video = $videoContent;

        switch ($args['template']) {
            case 'singlePost':
                $content = Markdown::defaultTransform($post->content);
                $args['post']->content = $videoContent . $content;
                break;
            case 'postTeaser':
                $args['post']->content = $videoContent . $post->summary;
                break;
        }
    }

    public function onBeforePostSaved(&$args) {
        $request = HamletCMS::request();
        if ($args['post']['type'] == 'video') {
            $args['post']['videosource'] = $request->getString('videosource', 'youtube');
            $args['post']['videoid'] = $request->getString('videoid', '');
            $args['post']['class'] = 'HamletCMS\\VideoPost\\VideoPost';
        }
    }

    public function onBeforeAutosave(&$args) {
        $request = HamletCMS::request();
        if ($args['post']['type'] == 'video') {
            $args['post']['videosource'] = $request->getString('videosource', 'youtube');
            $args['post']['videoid'] = $request->getString('videoid', '');
            // $args['post']['class'] = 'HamletCMS\\VideoPost\\VideoPost'; // needed?
        }
    }

    public function install()
    {
        $dbc = HamletCMS::databaseConnection();
        $dbc->query("ALTER TABLE `posts` ADD `videoid` varchar(20) NOT NULL, ADD `videosource` enum('youtube','vimeo') NOT NULL;");
        $dbc->query("ALTER TABLE `postautosaves` ADD `videoid` varchar(20) NOT NULL, ADD `videosource` enum('youtube','vimeo') NOT NULL;");
    }

    /**
     * Remove database records
     * 
     * @todo Do we want to remove all video posts or convert to standard?
     */
    public function uninstall()
    {
        $dbc = HamletCMS::databaseConnection();
        $dbc->query("ALTER TABLE `posts` DROP COLUMN `videoid`, DROP COLUMN `videosource`;");
        $dbc->query("ALTER TABLE `postautosaves` DROP COLUMN `videoid`, DROP COLUMN `videosource`;");
    }
}