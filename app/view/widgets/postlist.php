<?php
/****************************************************************
  Widget-postlist
  Prints out all of the posts in a blogger-style month view
****************************************************************/

class Postlist extends BlogCMSWidget {

    // Class Variables
    private $modelPosts;
    private $blog;
    
    public function __construct($settings, $blog, $modelBlog, $modelPosts, $modelComments, $modelContributors, $modelUsers)
    {
        // Constructor
        $this->modelPosts = $modelPosts;
        $this->blog = $blog;
        $this->config = $settings;
    }

    public function generate()
    {
        $lspostlist = '';
        $arrayPosts = $this->modelPosts->getAllPostsOnBlog($this->blog['id']);
        
        if(count($arrayPosts) != 0):

            $lspostlist.= '<h3>Post Archive</h3><div class="widget-content">';

            // Default Year-Month timestamp
            $current_year = 999999;
            $postnum = 0;

            foreach($arrayPosts as $post):
                $id = $post['id'];
                $title = $post['title'];

                // Get the post date as YYYYmm
                $post_year = date("Ym", strtotime($post['timestamp']));

                // If we have changed month (note that they have been fetched in order!)
                if($post_year < $current_year)
                {
                    // Show sub-heading
                    if($current_year != 999999) $lspostlist.= "</div></ul>";
                    $lspostlist.= "<h4 id='postlist-heading-{$post_year}' class='postlist-heading'>".date("F Y", strtotime($post['timestamp']))."</h4>".PHP_EOL;
                    $lspostlist.= "<ul id='postlist-$post_year' class='postlist'><div id='postlist-section-$post_year'>";
                    if($postnum > 0)
                    {
                        $lspostlist.= "<script>$('#postlist-section-$post_year').hide(); $('#postlist-heading-{$post_year}').click(function(){ $('#postlist-section-$post_year').toggle();});</script>";
                    }
                    else
                    {
                        $lspostlist.= "<script>$('#postlist-heading-{$post_year}').click(function(){ $('#postlist-section-$post_year').toggle();});</script>";
                    }
                    $current_year = $post_year;
                }

                if(defined('EXTERNAL_DOMAIN') && EXTERNAL_DOMAIN == 1)
                {
                    $blogdir = '';
                }
                else
                {
                    $blogdir = '/blogs/'.$post['blog_id'];
                }

                // Show post detail
                $lspostlist.= "<li><a href='".$blogdir."/posts/".$post['link']."'>$title</a></li>".PHP_EOL;

                $postnum++;

            endforeach;

            $lspostlist.= '</div></ul></div>';

        endif;

        return $lspostlist;
    }
}
?>