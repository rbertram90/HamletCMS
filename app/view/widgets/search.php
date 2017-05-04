<?php
/****************************************************************
  Widget-profile
  Shows a brief biography of the author (owner) of the blog
****************************************************************/

class Search extends BlogCMSWidget {

    // Class Variables
    private $modelUsers;
    private $blog;
    
    public function __construct($settings, $blog, $modelBlog, $modelPosts, $modelComments, $modelContributors, $modelUsers) {
        // Constructor
        $this->modelUsers = $modelUsers;
        $this->blog = $blog;
        
        $this->defaults = array(
            'name' => ''
        );
        
        $this->config = $settings;
		$this->setupConfig();
    }    
    
    public function generate() {
                
        if(array_key_exists('name', $this->config)) $output = '<h3>'.$this->config['name'].'</h3>';
        
return $output.<<<SEARCHFORM

    <form action="/blogs/{$this->blog['id']}/search" method="GET">
        <input type='text' placeholder="Search Blog" name='q' /><input type='submit' name='go' value='Search' />
    </form>
    
SEARCHFORM;
        
    }
}
?>