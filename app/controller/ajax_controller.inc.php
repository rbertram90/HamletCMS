<?php
namespace rbwebdesigns\blogcms;

use rbwebdesigns\core\Sanitize;

/**
 * This controller will be no longer
 * 
 * Will be moving methods over to their appropriate controller (posts/blogs)
 */

class AjaxController extends GenericController
{
    protected $modelPosts;
    protected $modelBlogs;
    
    public function __construct()
    {
        $this->modelPosts = BlogCMS::model('\rbwebdesigns\blogcms\model\Posts');
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\model\Blogs');
    }
    
    public function route($params)
    {
        $method = ($params == false) ? null : Sanitize::string($params[0]);
        
        switch($method)
        {
            case 'usercard':
                require SERVER_ROOT.'/app/ajax/usercard.php';
                break;
                
            case 'finduser':
                require SERVER_ROOT.'/app/ajax/usersearch.php';
                break;
                
            case 'get_posts':
                $modelPosts = new ClsPost($this->db);
                $modelUsers = $GLOBALS['modelUsers'];
                require SERVER_ROOT.'/app/ajax/get_posts_ajax.php';
                break;
                
            case 'add_image':
                require SERVER_ROOT.'/app/ajax/add_image_to_post.php';
                break;
            
            case 'widget_settings':
                require SERVER_ROOT.'/app/ajax/widget_settings.php';
                break;
            
            case 'widget_list':
                if(array_key_exists('element', $_GET)) {
                    $elem = Sanitize::string($_GET['element']);
                    require SERVER_ROOT.'/app/ajax/get_widget_list.php';
                }
                else {
                    echo "No element selected";
                }
                break;

            case 'check_title':
                require SERVER_ROOT.'/app/ajax/ajax_checkDuplicateTitle.php';
                break;
                                
            case 'drop_image_upload':
                
                if(isset($_GET['blogid'])) {
                    $BlogID = Sanitize::string($_GET['blogid']);
                }
                else
                {
                    http_response_code(500);
                    die("Could not retrieve blog information");
                }
                
                // Check User Permissions
                $blogModel = new ClsBlog($this->db);
                $blogModel->canWrite($BlogID);
                    
                require SERVER_ROOT.'/app/ajax/drop_image_upload.php';
                break;
        }
        
        return;
    }

    public function autosave(&$request, &$response)
    {
        $postID = $request->getInt('fld_postid');

        $data = [
            'title'         => $request->getString('fld_title'),
            'content'       => $request->getString('fld_content'),
            'tags'          => $request->getString('fld_tags'),
            'allowcomments' => $request->getInt('fld_allowcomments'),
            'type'          => $request->getString('fld_type'),
        ];

        $updateDB = $this->modelPosts->autosavePost($postID, $data);

        if($updateDB === false) {
            echo json_encode([
                'status' => 'failed',
                'message' => 'Could not run autosave - DB Update Error'
            ]);
        }
        elseif($updateDB > 0 && $updateDB !== $postID) {
            echo json_encode([
                'status' => 'success',
                'message' => 'Post autosaved at ' . date('H:i'),
                'newpostid' => $updateDB
            ]);
        }
        else {
            echo json_encode([
                'status' => 'success',
                'message' => 'Post autosaved at ' . date('H:i')
            ]);
        }
    }
    
    /**
     * @todo exclude current post ID!!!
     */
    public function checkDuplicateTitle(&$request, &$response)
    {
        $blogID = $request->getInt('blog_id');
        $postID = $request->getInt('post_id', 0);
        $title = $request->getString('post_title', '');

        if (strlen($title) == 0) {
            print "true";
            return;
        } 

        $link = $this->modelPosts->createSafePostUrl($title);
        
        $matchingPosts = $this->modelPosts->count(['blog_id' => $blogID, 'link' => $link]);
        if ($matchingPosts == 0) {
            print "false";
            return;
        }
        elseif ($matchingPosts == 1) {
            $post = $this->modelPosts->getPostByURL($link, $blogID);
            // Valid if new post & only one match or if the found post
            // is the one we're editing
            if ($postID == 0 || $post['id'] == $postID) {
                print "false";
                return;
            }
        }

        print "true";
    }

}
