<?php

namespace HamletCMS\Widgets\widgets;

use HamletCMS\Widgets\AbstractWidget;
use HamletCMS\HamletCMS;

class AlsoTagged extends AbstractWidget
{

    public function render() {
        if ($postID = $this->request->get('postID')) {
            /** @var \HamletCMS\BlogPosts\model\Posts */
            $postsModel = HamletCMS::model('\HamletCMS\BlogPosts\model\Posts');
            $post = $postsModel->getPostById($postID, HamletCMS::$blogID);
            $posts = [];

            foreach ($post->tags() as $tag) {
                $tag = str_replace('+', ' ', $tag); // would be nice to clean this up
                $posts[$tag] = $postsModel->getBlogPostsByTag(HamletCMS::$blogID, $tag)['posts'];
            }
            $this->response->setVar('current_post', $post);
            $this->response->setVar('posts', $posts);
        }
        

        $this->response->write('alsotagged.tpl', 'Widgets');
    }

}