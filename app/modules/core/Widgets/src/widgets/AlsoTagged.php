<?php

namespace rbwebdesigns\HamletCMS\Widgets\widgets;

use rbwebdesigns\HamletCMS\Widgets\AbstractWidget;
use rbwebdesigns\HamletCMS\HamletCMS;

class AlsoTagged extends AbstractWidget
{

    public function render() {
        if ($postID = $this->request->get('postID')) {
            $postsModel = HamletCMS::model('\rbwebdesigns\HamletCMS\BlogPosts\model\Posts');
            $post = $postsModel->getPostById($postID, HamletCMS::$blogID);
            $posts = [];

            foreach ($post->tags() as $tag) {
                $tag = str_replace('+', ' ', $tag); // would be nice to clean this up
                $posts[$tag] = $postsModel->getBlogPostsByTag(HamletCMS::$blogID, $tag);
            }

            $this->response->setVar('current_post', $post);
            $this->response->setVar('posts', $posts);
        }
        

        $this->response->write('alsotagged.tpl', 'Widgets');
    }

}