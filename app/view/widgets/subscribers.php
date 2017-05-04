<?php
class Subscribers implements BlogCMSWidget {

    // Class Variables
    private $modelUsers;
    private $blog;
    
    public function __construct($settings, $blog, $modelBlog, $modelPosts, $modelComments, $modelContributors, $modelUsers)
	{
        // Constructor
        $this->modelUsers = $modelUsers;
        $this->blog = $blog;
    }
    
    public function generate() {
    /*
        $lsRecentFollowers = "<h3>Recent Subscribers</h3>";

        foreach($parrFollowers as $follower):
            $lsRecentFollowers .= $follower['username'];
        endforeach;

        $lsRecentFollowers .= '<div class="push-right">';
        $lsRecentFollowers .= '	<a href="/blog/id/followers">More...</a>';
        $lsRecentFollowers .= '</div>';
    */
        $lsRecentFollowers = ""; // not ready to implement as social network is not up and running!

        return $lsRecentFollowers;
    }
}
?>