<?php
/****************************************************************
  Widget-taglist
  Prints out all of the tags and their counts
****************************************************************/

class Taglist extends BlogCMSWidget {

    // Class Variables
    private $modelPosts;
    private $blog;
    
    public function __construct($settings, $blog, $modelBlog, $modelPosts, $modelComments, $modelContributors, $modelUsers) {
        // Constructor
        $this->modelPosts = $modelPosts;
        $this->blog = $blog;
        $this->defaults = array(
            'name' => 'Tags',
            'orderby' => 'Count',
            'numtoshow' => 999,
            'lowerlimit' => 1,
            'display' => 'list'
        );
        $this->config = $settings;
        $this->setupConfig();
    }
    
    
    public function generate() {

        $arrayTags = $this->modelPosts->countAllTagsByBlog($this->blog['id']);
        if(strtolower($this->config['orderby']) == 'name') sksort($arrayTags, 0); // apply sort
        else sksort($arrayTags, 1);
        
        $tagList = '';
        $count = 0;
        
        foreach($arrayTags as $tag)
        {
        
            $count += 1;
            // Check limit set in config
            if($count > $this->config['numtoshow']) break;
            if($tag[1] < $this->config['lowerlimit']) break;

            // Put spaces back in!
            $tagName = str_replace("+", " ", $tag[0]);

            // Just Incase - for old (unsanitized) tags!
            $tagLink = str_replace(" ", "+", trim($tag[0]));
            
            if(defined('EXTERNAL_DOMAIN') && EXTERNAL_DOMAIN == 1) $blogdir = '';
            else $blogdir = '/blogs/'.$this->blog['id'];

            if($this->config['display'] == "list") {
                $tagList.= '<li><a href="'.$blogdir.'/tags/'.$tagLink.'">'.$tagName.' ('.$tag[1].')</a></li>'.PHP_EOL;
            }
            else
            {
                if($tag[1] < 4) $fontsize = '90%';
                elseif($tag[1] < 10) $fontsize = '100%';
                elseif($tag[1] < 20) $fontsize = '110%';
                elseif($tag[1] < 30) $fontsize = '120%';
                else $fontsize = '130%';
                
                $tagList.= '<a href="'.$blogdir.'/tags/'.$tagLink.'" style="font-size:'.$fontsize.'; display:inline;">'.$tagName.' ('.$tag[1].')</a> '.PHP_EOL;
            }

        }

        if($this->config['display'] == "list") $tagList = '<h3>'.$this->config['name'].'</h3><ul class="taglist">'.$tagList.'</ul>';
        else $tagList = '<h3>'.$this->config['name'].'</h3>'.$tagList;

        return $tagList;
    }
}
?>