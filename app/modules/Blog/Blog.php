<?php

namespace rbwebdesigns\blogcms;

class Blog
{
    public function onGenerateMenu($options)
    {
        if ($options['id'] == 'cms_main_actions') {
            $newLinks = [];
            $currentLinks = $options['menu']->getLinks();
            foreach ($currentLinks as $link) {
                // Check if the BLOG_ID token has been replaced - if not
                // remove the link
                if ($link->url) {
                    if (strpos($link->url, '{BLOG_ID}') === false) {
                        $newLinks[] = $link;
                    }
                }
                elseif (!BlogCMS::$blogID && strtolower($link->text) == 'blog actions') {
                    // Yes this is not clean - difficult scenario...
                    // don't want to show the "blog actions" label when no blog is selected
                    // @todo maybe add a new key to menu link for dependency?
                }
                else {
                    $newLinks[] = $link;
                }
            }
            $options['menu']->setLinks($newLinks);
        }
    }
}