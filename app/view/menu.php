<?php

namespace rbwebdesigns\blogcms;

class Menu
{
    /**
     * @var array
     */
    protected $links = [];

    /**
     * @param \rbwebdesigns\blogcms\MenuLink $link
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
}