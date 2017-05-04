<?php
/****************************************************************
  Widget-comments
  Prints out recent comments on the blog
****************************************************************/

class Comments extends BlogCMSWidget {
    
    // Class Variables
    private $modelComments;
    private $blog;
    protected $defaults;
    
    public function __construct($settings, $blog, $modelBlog, $modelPosts, $modelComments, $modelContributors, $modelUsers)
	{
        // Constructor
        $this->modelComments = $modelComments;
        $this->modelPosts = $modelPosts;
        $this->modelUsers = $modelUsers;
        $this->blog = $blog;
        $this->defaults = array(
            'name' => 'Recent Comments',
            'numbertoshow' => 5,
            'maxlength' => 200
        );
        $this->config = $settings;
		$this->setupConfig();
    }
    
    
    public function generate() {

        $arrayComments = $this->modelComments->getCommentsByBlog($this->blog['id']); // should we limit at this point?
                
        // Start output
        $lsRecentComments = '<h3>'.$this->config['name'].'</h3><ul>';
        
        $current = 1;
        foreach($arrayComments as $comment):
        
            // Check we haven't exceeeded the limit
            if($current > $this->config['numbertoshow']) break;
        
            // Format Message
            $message = substr($comment['message'], 0, $this->config['maxlength']);
            if(strlen($comment['message']) > $this->config['maxlength']) $message.= "...";
        
            // Get Post Information
            $arrayPost = $this->modelPosts->getPostById($comment['post_id']);
        
            // Get User Information
            $arrayUser = $this->modelUsers->getUserById($comment['user_id']);
        
            // Format date
            // $df = new DateFormatter();
            $friendlyTime = RBwebdesigns\DateFormatter::formatFriendlyTime($comment['timestamp']);
        
    if(defined('EXTERNAL_DOMAIN') && EXTERNAL_DOMAIN == 1) {
        $blogdir = '';
    }
    else {
        $blogdir = CLIENT_ROOT_BLOGCMS.'/blogs/'.$arrayPost['blog_id'];
    }
        
            // Output
            $lsRecentComments.= '<li>'.$message.'<br><div class="commentinfo">'.$friendlyTime.' by <a href="'.CLIENT_ROOT.'/users/'.$arrayUser['id'].'">'.$arrayUser['username'].'</a> on <a href="'.$blogdir.'/posts/'.$arrayPost['link'].'">'.$arrayPost['title'].'</a></div></li>'.PHP_EOL;
        
            $current++;
        
        endforeach;

        $lsRecentComments.= '</ul>';

        return $lsRecentComments;
    }
    
}
?>