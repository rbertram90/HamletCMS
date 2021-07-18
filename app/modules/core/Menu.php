<?php

namespace HamletCMS;

use rbwebdesigns\core\JSONHelper;

class Menu
{
    /**
     * @var array
     */
    protected $links = [];

    protected $key = '';


    /**
     * @param string $key
     *  Name of the menu
     */
    public function __construct($key = '')
    {
        if (strlen($key)) {
            $this->key = $key;
            $this->populateLinksFromCache();
        }
    }

    /**
     * @param \HamletCMS\MenuLink $link
     * 
     * @return self
     */
    public function addLink($link)
    {
        $this->links[] = $link;
        return $this;
    }
    
    /**
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }

    public function setLinks($links)
    {
        $this->links = $links;
    }

    /**
     * Generate the links from menu cache with menu ID passed in at constructor
     * Currently a protected function, would it be useful to be able to populate
     * these links after the object is initialised?
     */
    protected function populateLinksFromCache()
    {
        $cacheFilePath = SERVER_ROOT . '/cache/menus.json';

        if (!file_exists($cacheFilePath)) {
            HamletCMS::generateMenuCache();
        }

        $cache = JSONHelper::JSONFileToArray($cacheFilePath);

        if (!array_key_exists($this->key, $cache)) return false;

        foreach ($cache[$this->key] as $linkData) {
            $link = new MenuLink();

            switch ($linkData['type']) {
                case 'route':
                    $link->url = HamletCMS::route($linkData['route']);
                    if (!$link->url) continue 2;
                    break;
            }

            if (array_key_exists('text', $linkData)) {
                $link->text = $linkData['text'];
            }
            if (array_key_exists('subtext', $linkData)) {
                $link->subtext = $linkData['subtext'];
            }
            if (array_key_exists('icon', $linkData)) {
                $link->icon = $linkData['icon'];
            }
            if (HamletCMS::$activeMenuLink && $link->url == HamletCMS::$activeMenuLink) {
                $link->active = true;
            }

            $this->links[] = $link;
        }
    }

}