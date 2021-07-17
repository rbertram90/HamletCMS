<?php
namespace HamletCMS;

use Michelf\Markdown;

class VideoPost
{
    public function onViewEditPost($args) {
        if ($args['type'] != 'video') return;

        $controller = new VideoPost\controller\VideoPost();
        $controller->edit();
    }

    public function runTemplate($args)
    {
        $post = $args['post'];
        if ($post->type !== 'video') return;

        switch($post->videosource) {
            case 'youtube':
                $videoContent = '<iframe width="100%" style="max-width:560px;" height="315" src="//www.youtube.com/embed/'. $post->videoid .'" frameborder="0" allowfullscreen></iframe>';
                break;

            case 'vimeo':
                $videoContent = '<iframe src="//player.vimeo.com/video/'. $post->videoid .'?title=0&amp;byline=0&amp;portrait=0&amp;color=fafafa" width="560" height="315" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>';
                break;
        }

        switch ($args['template']) {
            case 'singlePost':
                $content = Markdown::defaultTransform($post->content);
                $args['post']->trimmedContent = $videoContent . $content;
                break;
            case 'postTeaser':
                $args['post']->trimmedContent = $videoContent . $post->summary;
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