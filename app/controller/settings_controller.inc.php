<?php
namespace rbwebdesigns\blogcms;

use rbwebdesigns\core\Sanitize;
use rbwebdesigns\core\JSONHelper;
use rbwebdesigns\core\AppSecurity;

/**
 * class SettingsController
 *
 * This is the controller which acts as the intermediatory between the
 * model (database) and the view. Any requests to the model are sent from
 * here rather than the view.
 */
class SettingsController extends GenericController
{
    /**
     * @var \rbwebdesigns\blogcms\model\Blogs
     */
    protected $modelBlogs;
    /**
     * @var \rbwebdesigns\blogcms\model\Posts
     */
    protected $modelPosts;
    /**
     * @var \rbwebdesigns\blogcms\model\Comments
     */
    protected $modelComments;
    /**
     * @var \rbwebdesigns\blogcms\model\AccountFactory
     */
    protected $modelUsers;
    /**
     * @var \rbwebdesigns\blogcms\model\Contributors
     */
    protected $modelContributors;
    
    public function __construct()
    {
        // Initialise Models
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\model\Blogs');
        $this->modelContributors = BlogCMS::model('\rbwebdesigns\blogcms\model\Contributors');
        $this->modelPosts = BlogCMS::model('\rbwebdesigns\blogcms\model\Posts');
        $this->modelComments = BlogCMS::model('\rbwebdesigns\blogcms\model\Comments');
        $this->modelUsers = BlogCMS::model('\rbwebdesigns\blogcms\model\AccountFactory');
    }
    
    /**
     * Handles /settings/menu
     */
    public function menu(&$request, &$response)
    {
        $blogID = $request->getUrlParameter(1);
        $blog = $this->modelBlogs->getBlogById($blogID);

        $response->setVar('blog', $blog);
        $response->setTitle('Blog Settings - ' . $blog['name']);
        $response->write('settings/menu.tpl');
    }

    /**
     * Handles /settings/general/<blogid>
     * Edit blog name, description etc.
     */
    public function general(&$request, &$response)
    {
        $blogID = $request->getUrlParameter(1);
        $blog = $this->modelBlogs->getBlogById($blogID);

        if ($request->method() == 'POST') return $this->action_updateBlogGeneral($request, $response, $blog);

        $response->setVar('blog', $blog);
        $response->setTitle('General Settings - ' . $blog['name']);
        $response->setVar('categorylist', BlogCMS::config()['blogcategories']);
        $response->write('settings/general.tpl');
    }

    /**
     * Handles /settings/posts/<blogid>
     * Edit post display settings
     */
    public function posts(&$request, &$response)
    {
        $blogID = $request->getUrlParameter(1);
        $blog = $this->modelBlogs->getBlogById($blogID);

        if ($request->method() == 'POST') return $this->action_updatePostsSettings($request, $response, $blog);

        $postConfig = $this->getBlogConfig($blog['id']);

        if (isset($postConfig['posts'])) {
            // Default values where needed
            if(!isset($postConfig['posts']['postsperpage'])) $postConfig['posts']['postsperpage'] = 5;
            if(!isset($postConfig['posts']['postsummarylength'])) $postConfig['posts']['postsummarylength'] = 200;
            $response->setVar('postConfig', $postConfig['posts']);
        }
        else {
            // No posts config exists - send defaults
            $response->setVar('postConfig', ['postsperpage' => 5, 'postsummarylength' => 200]);
        }

        $response->setVar('blog', $blog);
        $response->setTitle('Post Settings - ' . $blog['name']);
        $response->write('settings/posts.tpl');
    }

    /**
     * Handles /settings/header/<blogid>
     */
    public function header(&$request, &$response)
    {
        $blogID = $request->getUrlParameter(1);
        $blog = $this->modelBlogs->getBlogById($blogID);

        if ($request->method() == 'POST') return $this->action_updateHeaderContent($blog);
        
        $blogconfig = $this->getBlogConfig($blog['id']);
        if (isset($blogconfig['header'])) $response->setVar('blogconfig', $blogconfig['header']);
        else $response->setVar('blogconfig', []);

        $response->setVar('blog', $blog);
        $response->addScript('/resources/js/rbwindow');
        $response->addScript('/resources/js/rbrtf');
        $response->addStylesheet('/resources/css/rbrtf');
        $response->addStylesheet('/resources/css/rbwindow');
        $response->setTitle('Customise Blog Header - ' . $blog['name']);
        $response->write('settings/header.tpl');        
    }

    /**
     * Handles /settings/footer/<blogid>
     */
    public function footer(&$request, &$response)
    {
        $blogID = $request->getUrlParameter(1);
        $blog = $this->modelBlogs->getBlogById($blogID);

        if ($request->method() == 'POST') return $this->action_updateFooterContent($blog);
        
        $blogconfig = $this->getBlogConfig($blog['id']);
        if (isset($blogconfig['footer'])) $response->setVar('blogconfig', $blogconfig['footer']);
        else $response->setVar('blogconfig', []);

        $response->setVar('blog', $blog);
        $response->addScript('/resources/js/rbwindow.js');
        $response->addScript('/resources/js/rbrtf.js');
        $response->addStylesheet('/resources/css/rbrtf.css');
        $response->addStylesheet('/resources/css/rbwindow.css');
        $response->setTitle('Customise Blog Footer - ' . $blog['name']);
        $response->write('settings/footer.tpl');
    }

    /**
     * Handles /settings/pages/<blogid>
     */
    public function pages(&$request, &$response)
    {
        $blogID = $request->getUrlParameter(1);
        $blog = $this->modelBlogs->getBlogById($blogID);

        $action = $request->getUrlParameter(2);
        $actionTaken = true;

        switch ($action) {
            case 'add'    : $result = $this->action_addPage($request, $response, $blog); break;
            case 'up'     : $result = $this->action_movePageUp($request, $response, $blog); break;
            case 'down'   : $result = $this->action_movePageDown($request, $response, $blog); break;
            case 'remove' : $result = $this->action_removePage($request, $response, $blog); break;
            default: $actionTaken = false;
        }

        if($actionTaken && $result) {
            BlogCMS::Session()->addMessage('Page list updated', 'success');
        }
        elseif($actionTaken && !$result) {
            BlogCMS::Session()->addMessage('Failed to update page list', 'error');
        }

        $pagelist = explode(',', $blog['pagelist']);
        $pages = $taglist = [];

        foreach ($pagelist as $postID) {
            if (is_numeric($postID)) {
                $pages[] = $this->modelPosts->get('*', ['id' => $postID], '', '', false);
            }
            elseif (substr($postID, 0, 2) == 't:') {
                $taglist[] = substr($postID, 2);
                $pages[] = $postID;
            }
        }
        
        $tags = $this->modelPosts->getAllTagsByBlog($blog['id']);
        $posts = $this->modelPosts->get(['id', 'title'], ['blog_id' => $blog['id']]);

        $response->setVar('pagelist', $pagelist);
        $response->setVar('pages', $pages);
        $response->setVar('taglist', $taglist);
        $response->setVar('tags', $tags);
        $response->setVar('posts', $posts);
        $response->setVar('blog', $blog);
        $response->setTitle('Manage Pages - ' . $blog['name']);
        $response->write('settings/pages.tpl');
    }

    /**
     * Handles /settings/stylesheet/<blogid>
     */
    public function stylesheet(&$request, &$response)
    {
        $blogID = $request->getUrlParameter(1);
        $blog = $this->modelBlogs->getBlogById($blogID);

        if($request->method() == 'POST') return $this->action_saveStylesheet($params);

        $response->setVar('serverroot', SERVER_ROOT);
        $response->setVar('blog', $blog);
        $response->setTitle('Edit Stylesheet - ' . $blog['name']);
        $response->write('settings/stylesheet.tpl');
    }

    /**
     * Handles /settings/stylesheet/<blogid>
     */
    public function template(&$request, &$response)
    {
        $blogID = $request->getUrlParameter(1);
        $blog = $this->modelBlogs->getBlogById($blogID);

        if ($request->method() == 'POST') return $this->action_applyNewTemplate($request, $response);

        $response->setVar('blog', $blog);
        $response->setTitle('Choose Template - ' . $blog['name']);
        $response->write('settings/template.tpl');
    }

    /**
     * Handles /settings/blogdesigner/<blogid>
     * 
     * @todo find a way to run a non-smarty template
     */
    public function blogdesigner(&$request, &$response)
    {
        $blogID = $request->getUrlParameter(1);
        $blog = $this->modelBlogs->getBlogById($blogID);

        if ($request->method() == 'POST') return $this->action_updateBlogDisplaySettings($blog);

        $response->setVar('blog', $blog);
        $response->setTitle('Blog Designer - ' . $blog['name']);
        $response->addScript('/resources/colorpicker/jscolor.js');
        $response->write(SERVER_ROOT.'/app/view/settings/blogdesigner.php', array('blog' => $blog, 'cms_db' => $this->db));
    }
        
    /**
     * Handles /settings/widgets/<blogid>
     */
    public function widgets(&$request, &$response)
    {
        $blogID = $request->getUrlParameter(1);
        $blog = $this->modelBlogs->getBlogById($blogID);

        if ($request->method() == 'POST') return $this->action_updateWidgets($blog);
        
        $response->setTitle('Customise Widgets - ' . $blog['name']);
        $response->setVar('blog', $blog);
        $response->setVar('widgetconfig', $this->getWidgetConfig($blog['id']));
        $response->setVar('installedwidgets', $this->getInstalledWidgets());
        $response->write('settings/widgets3.tpl');
    }

    /**
        Old (?) was used in header section however we now use isset - though not
        properly tested.
    **/
    public function getHeaderValue($parray, $key)
    {
        if(gettype($parray) == 'array' && array_key_exists('header', $parray))
        {
            if(array_key_exists($key, $parray['header']))
            {
                return $parray['header'][$key];
            }
        }
        return ""; // not found
    }
    
    
    /******************************************************************
        POST - Blog Settings
    ******************************************************************/
    
    /**
     * View a specific widget config page
     */
    private function viewWidgetSpecificSettings($blog, $widgetname)
    {
        $settings_view = SERVER_ROOT.'/app/view/settings/widgets_'.$widgetname.'.php';
        
        if(file_exists($settings_view)) $this->view->render($settings_view, array('blog' => $blog));
        else $this->throwNotFound();
    }
    
    /**
     * Run update for name and description of a blog
     * @param array $blog
     */
    public function action_updateBlogGeneral($request, $response, $blog)
    {
        $update = $this->modelBlogs->update(['id' => $blog['id']], [
            'name'        => $request->getString('fld_blogname'),
            'description' => $request->getString('fld_blogdesc'),
            'visibility'  => $request->getString('fld_blogsecurity'),
            'category'    => $request->getString('fld_category')
        ]);

        if($update) {
            $response->redirect('/settings/general/' . $blog['id'], "Blog settings updated", "success");
        }
        else {
            $response->redirect('/settings/general/' . $blog['id'], "Error saving to database", "error");
        }
    }
    
    /**
     *  Update how posts are displayed on the blog
     */
    public function action_updatePostsSettings($request, $response, $blog)
    {
        $update = $this->updateBlogConfig($blog['id'], [
            'posts' => [
                'dateformat'        => $request->getString('fld_dateformat'),
                'timeformat'        => $request->getString('fld_timeformat'),
                'postsperpage'      => $request->getInt('fld_postsperpage'),
                'allowcomments'     => $request->getInt('fld_commentapprove'),
                'postsummarylength' => $request->getInt('fld_postsummarylength'),
                'showtags'          => $request->getString('fld_showtags'),
                'dateprefix'        => $request->getString('fld_dateprefix'),
                'dateseperator'     => $request->getString('fld_dateseperator'),
                'datelocation'      => $request->getString('fld_datelocation'),
                'timelocation'      => $request->getString('fld_timelocation'),
                'showsocialicons'   => $request->getString('fld_showsocialicons'),
                'shownumcomments'   => $request->getString('fld_shownumcomments')
            ]
        ]);
        
        if($update) {
            $response->redirect('/settings/posts/' . $blog['id'], "Post settings updated", "success");
        }
        else {
            $response->redirect('/settings/posts/' . $blog['id'], "Error saving to database", "error");
        }
    }
    
    /**
     * Get the blog config file JSON
     * 
     * @param int $blogid
     * 
     * @return array
     */
    protected function getBlogConfig($blogid)
    {
        return JSONhelper::JSONFileToArray(SERVER_PUBLIC_PATH . "/blogdata/{$blogid}/config.json");
    }
    
    /**
     * Save to the blog config file
     * 
     * @param int $blogid
     * @param array $blogConfig
     * 
     * @return bool Was the save successful?
     */
    protected function saveBlogConfig($blogid, $blogConfig)
    {
        $json_string = JSONhelper::arrayToJSON($blogConfig);
        $save = file_put_contents(SERVER_PUBLIC_PATH . "/blogdata/{$blogid}/config.json", $json_string);
        return $save !== false;
    }
    
    /**
     * Update the blog configuration file with new values
     * Note that new arrays are created if needs be.
     */
    protected function updateBlogConfig($blogid, $data)
    {
        $settings = $this->getBlogConfig($blogid);
        $settings = array_replace_recursive($settings, $data);
        return $this->saveBlogConfig($blogid, $settings);
    }
    
    /**
        Type: POST
        Description: Update the content in the footer
    **/
    public function action_updateFooterContent($blog)
    {
        // Change Settings
        $this->updateBlogConfig($blog['id'], array(
            'footer' => array(
                'numcols' => safeString($_POST['fld_numcolumns']),
                'content_col1' => safeString($_POST['fld_contentcol1']),
                'content_col2' => safeString($_POST['fld_contentcol2']),
                'background_image' => safeString($_POST['fld_footerbackgroundimage']),
                'bg_image_post_horizontal' => safeString($_POST['fld_horizontalposition']),
                'bg_image_post_vertical' => safeString($_POST['fld_veritcalposition'])
            )
        ));
        
        // Output
        setSystemMessage(ITEM_UPDATED, "Success");
        redirect('/config/'.$blog['id'].'/footer');
    }
    
    
    /**
        Type: POST
        Description: Update the content in the header
    **/
    public function action_updateHeaderContent($blog)
    {
        // Change Settings
        $this->updateBlogConfig($blog['id'], array(
            'header' => array(
                'background_image' => safeString($_POST['fld_headerbackgroundimage']),
                'bg_image_post_horizontal' => safeString($_POST['fld_horizontalposition']),
                'bg_image_post_vertical' => safeString($_POST['fld_veritcalposition']),
                'bg_image_align_horizontal' => safeString($_POST['fld_horizontalalign']),
                'hide_title' => safeString($_POST['fld_hidetitle']),
                'hide_description' => safeString($_POST['fld_hidedescription'])
            )
        ));
        
        // Output
        setSystemMessage(ITEM_UPDATED, "Success");
        redirect('/config/'.$blog['id'].'/header');
    }
    
    /**
     * Add a page to the list of pages to show on menu
     * 
     * @param $request
     * @param $response
     * @param array $blog
     * 
     * @return bool Was the page added successfully?
     */
    public function action_addPage(&$request, &$response, &$blog)
    {
        if (!$pageType = $request->getString('fld_pagetype', false)) return false;
        
        switch ($pageType) {
            case 'p':
                // Check that post we're adding is valid
                $newpageID = $request->getInt('fld_postid');
                $targetpost = $this->modelPosts->get(['blog_id'], ['id' => $newpageID], '', '', false);
                if (!$targetpost || $targetpost['blog_id'] != $blog['id']) {
                    return false;
                }
                break;
                
            case 't':
                if (!$tag = $request->getString('fld_tag', false)) return false;
                $tag = str_replace(',', '', $tag);
                $newpageID = 't:' . $tag;
                break;

            default:
                return false;
        }

        // Update Current Page List
        if (array_key_exists('pagelist', $blog) && strlen($blog['pagelist']) > 0) {
            $pagelist = $blog['pagelist'] . ',' . $newpageID;
        }
        else {
            $pagelist = $newpageID;
        }

        // Make sure we've got the most recent data for this request
        $blog['pagelist'] = $pagelist;
        
        return $this->modelBlogs->update(['id' => $blog['id']], ['pagelist' => $pagelist]);
    }
    
    /**
     * @param $request
     * @param $response
     * @param array $blog
     * 
     * @return bool Was the page removed successfully?
     */
    public function action_removePage(&$request, &$response, &$blog)
    {
        if (!$page = $request->getString('fld_postid', false)) return false;
        
        // Get position of page in the comma seperated list
        $pagelist = explode(',', $blog['pagelist']);
        $idKey = array_search($page, $pagelist);
        if ($idKey === false) return false;

        // Remove page from list
        array_splice($pagelist, $idKey, 1);
        
        // Make sure we've got the most recent data for this request
        $blog['pagelist'] = implode(',', $pagelist);

        return $this->modelBlogs->update(['id' => $blog['id']], [
            'pagelist' => $blog['pagelist']
        ]);
    }
    
    /**
     * @param $request
     * @param $response
     * @param array $blog
     * 
     * @return bool Was the page moved successfully?
     */
    public function action_movePageUp(&$request, &$response, &$blog)
    {
        if (!$page = $request->getString('fld_postid', false)) return false;
        
        $pagelist = explode(',', $blog['pagelist']);
        $idKey = array_search($page, $pagelist);
        
        if ($idKey !== false && $idKey > 0) {
            $pagelist[$idKey] = $pagelist[$idKey - 1];
            $pagelist[$idKey - 1] = $page;
            $pagelist = implode(',', $pagelist);

            $blog['pagelist'] = $pagelist;

            return $this->modelBlogs->update(['id' => $blog['id']], ['pagelist' => $pagelist]);
        }

        return false;
    }
    
    /**
     * @param $request
     * @param $response
     * @param array $blog
     * 
     * @return bool Was the page moved successfully?
     */
    public function action_movePageDown(&$request, &$response, &$blog)
    {
        if (!$page = $request->getString('fld_postid', false)) return false;
        
        $pagelist = explode(',', $blog['pagelist']);
        $idKey = array_search($page, $pagelist);
        
        if ($idKey !== false && $idKey < count($pagelist) - 1) {
            $pagelist[$idKey] = $pagelist[$idKey + 1];
            $pagelist[$idKey + 1] = $page;
            $pagelist = implode(',', $pagelist);
            
            $blog['pagelist'] = $pagelist;

            return $this->modelBlogs->update(['id' => $blog['id']], ['pagelist' => $pagelist]);
        }
        
        return false;
    }
    
    /**
     * New - works in reverse of previous version
     * This is becuase we want to have values in JSON
     * that we don't want to send to the browser
     *  
     * The previous version literally re-generated the
     * whole JSON every time the blog designer form
     * was submitted...
     *  
     * This one loops through the JSON stored on the
     * server and looks for the corresponding value
     * in $_POST
    **/
    private function action_updateBlogDisplaySettings($blog)
    {
        $log = "";
        
        $arraySettings = jsonToArray(SERVER_PATH_BLOGS.'/'.$blog['id'].'/template_config.json');
        // Loop through JSON array
        // $log.= "looping through saved JSON<br>";
        
        foreach($arraySettings as $group => $groupdata)
        {        
            if(strtolower($group) == 'layout') continue;
            
            // $log.= "<b>processing {$group}</b><br>";
            // $log.= "looking for [displayfield][{$group}] in POST<br>";
            $postdata = $_POST['displayfield'][$group];
            
            // Check fields supplied in POST
            if(is_array($postdata))
            {
                // $log.= "found in post<br>";
                // $log.= "looping through [displayfield][{$group}] in POST<br>";
                
                for($i = 1; $i < count($groupdata); $i++)
                {
                    // Check that the name from the config is in $_POST
                    // echo array_key_exists('label', $groupdata[$i]);
                    $fieldname = str_replace(' ', '_', $groupdata[$i]['label']);
                    
                    // $log.= "checking for ".$fieldname."(json) in POST<br>";
                    
                    if(array_key_exists($fieldname, $postdata))
                    {
                        // $log.= "found ".$fieldname."<br>";
                        
                        // Yes!
                        if(array_key_exists('defaultfield', $_POST) && array_key_exists($group, $_POST['defaultfield']) && array_key_exists($fieldname, $_POST['defaultfield'][$group]))
                        {
                            $defaultdata = $_POST['defaultfield'][$group][$fieldname];
                        }
                        else
                        {
                            $defaultdata = "off"; // may not be sent
                        }
                        
                        if(strtolower($defaultdata) == "on")
                        {
                            // Value is default
                            // echo "Reverting {$group} {$i} to default<br>";
                            $arraySettings[$group][$i]['current'] = $arraySettings[$group][$i]['default'];
                        }
                        else
                        {
                            // echo "setting {$group} {$i} current -> {$postdata[$fieldname]}<br>";
                            $arraySettings[$group][$i]['current'] = $postdata[$fieldname];
                        }
                    }
                } // inner loop
            }
        } // outer loop
        
        // die($log);
        
        // Save the config file back
        file_put_contents(SERVER_PATH_BLOGS.'/'.$blog['id'].'/template_config.json', json_encode($arraySettings));
        
        setSystemMessage(ITEM_UPDATED, "Success");
        redirect('/config/'.$blog['id']);
    }
    
    
    /**
        Replace the files needed to copy a new template to the blog
    **/
    private function applyNewBlogTemplate($blog_key, $template_id)
    {
        // Update default.css
        $copy_css = SERVER_PATH_TEMPLATES.'/stylesheets/'.$template_id.'.css';
        $new_css = SERVER_PATH_BLOGS.'/'.$blog_key.'/default.css';
        if (!copy($copy_css, $new_css)) die(showError('failed to copy '.$template_id.'.css'));
        
        // Update template_config.json
        $copy_json = SERVER_PATH_TEMPLATES.'/stylesheets/'.$template_id.'.json';
        $new_json = SERVER_PATH_BLOGS.'/'.$blog_key.'/template_config.json';
        if (!copy($copy_json, $new_json)) die(showError('failed to copy '.$template_id.'.json'));
    }
    
    
    /**
        Apply a completely new template from the predefined templates
    **/
    public function action_applyNewTemplate($DATA, $params)
    {
        // Sanitize Variables
        $blog_key = safeNumber($params[0]);
        $template_id = safeString($_POST['template_id']);
        $currentUser = BlogCMS::session()->currentUser;

        // Check we have permission to perform action
        if(!$this->modelContributors->isBlogContributor($blog_key, $currentUser)) return $this->throwAccessDenied();
        // Apply the new template
        $this->applyNewBlogTemplate($blog_key, $template_id);
        // Redirect Home
        setSystemMessage(ITEM_UPDATED, "Success");
        redirect('/config/'.$blog_key);
    }
    
    
    /**
        Save changes made to the stylesheet
    **/
    public function action_saveStylesheet($params)
    {
        // Sanitize Variables
        $css_string = strip_tags($_POST['fld_css']);
        $blog_id = Sanitize::int($params[0]);
        $currentUser = BlogCMS::session()->currentUser;

        // Check we have permission to perform action
        if(!$this->modelContributors->isBlogContributor($blog_id, $currentUser)) return $this->throwAccessDenied();
        // Update default.css
        if(is_dir(SERVER_PUBLIC_PATH.'/blogdata/'.$blog_id))
        {
            file_put_contents(SERVER_PATH_BLOGS.'/'.$blog_id.'/default.css', $css_string);
            setSystemMessage(ITEM_UPDATED, "Success");
        }
        else
        {
            setSystemMessage("An error has occured - please contact support", "Error");
        }
        redirect('/config/'.$blog_id.'/stylesheet');
    }
    
    
    /**
        Get some JSON which is okay to pass to the view - as widgets are dynamic if any are missing in the config
        then we still want them to show in the options menu
    **/
    public function checkWidgetJSON($blog, &$widgetconfig)
    {
        $arrayBlogConfig = jsonToArray(SERVER_PATH_BLOGS.'/'.$blog['id'].'/template_config.json');
        
        // Config
        //  -> Layout
        //     -> ColumnCount
        //  -> Header
        //  -> Footer
        //  -> (Leftpanel)
        //  -> (Rightpanel)
        
        $numcolumns = (array_key_exists('Layout', $arrayBlogConfig) && array_key_exists('ColumnCount', $arrayBlogConfig['Layout'])) ? $arrayBlogConfig['Layout']['ColumnCount'] : 2;
        
        if(!array_key_exists('header', $widgetconfig)) $widgetconfig['header'] = array();
        if(!array_key_exists('footer', $widgetconfig)) $widgetconfig['footer'] = array();
        
        switch($numcolumns) {
            case 3:
                // 2 Columns for widgets
                if(!array_key_exists('leftpanel', $widgetconfig)) $widgetconfig['leftpanel'] = array();
                if(!array_key_exists('rightpanel', $widgetconfig)) $widgetconfig['rightpanel'] = array();
                break;
                
            case 2:                
                $postscolumnnumber = (array_key_exists('Layout', $arrayBlogConfig) && array_key_exists('PostsColumn', $arrayBlogConfig['Layout'])) ? $arrayBlogConfig['Layout']['PostsColumn'] : 1;
                // 1 column for widgets
                if($postscolumnnumber == 2) {
                    if(!array_key_exists('leftpanel', $widgetconfig)) $widgetconfig['leftpanel'] = array();
                }
                else {
                    if(!array_key_exists('rightpanel', $widgetconfig)) $widgetconfig['rightpanel'] = array();
                }
                break;
                
            case 1:
                // Don't show any columns
                break;
        }
    }
    
    
    /**
     * Create the settings.json file
     * @param int blog ID
     * @return array containing widget settings
     */
    public function getWidgetConfig($blogID)
    {
        $widgetSettingsFilePath = SERVER_PATH_BLOGS . '/' . $blogID . '/widgets.json';
        
        if(file_exists($widgetSettingsFilePath))
        {
            return JSONhelper::JSONFileToArray($widgetSettingsFilePath);
        }
        
        return $this->createWidgetSettingsFile($blogID);
    }
    
    
    /**
     * Create the settings.json file
     * @param int blog ID
     * @return array containing default widget settings
     */
    public function createWidgetSettingsFile($blogID)
    {
        if($widgetConfigFile = fopen(SERVER_PATH_BLOGS . '/' . $blogID . '/widgets.json', 'w'))
        {
            $defaultWidgetConfig = array('Header' => []);
            $templateConfig = rbwebdesigns\JSONhelper::JSONFileToArray(SERVER_PATH_BLOGS.'/' . $blogID . '/template_config.json');
            
            if(multiarray_key_exists($templateConfig, 'Layout.ColumnCount'))
            {
                switch($templateConfig['Layout']['ColumnCount'])
                {
                    case 3:
                        $defaultWidgetConfig['RightPanel'] = [];
                        $defaultWidgetConfig['LeftPanel'] = [];
                        break;
                        
                    case 2:
                        if(multiarray_key_exists($templateConfig, 'Layout.PostsColumn'))
                        {
                            if($templateConfig['Layout']['PostsColumn'] == 2) $defaultWidgetConfig['LeftPanel'] = [];
                            else $defaultWidgetConfig['RightPanel'] = [];
                        }
                        break;
                }
            }
            
            $defaultWidgetConfig['Footer'] = [];
            
            fwrite($widgetConfigFile, JSONhelper::arrayToJSON($defaultWidgetConfig));
            fclose($widgetConfigFile);
            return $defaultWidgetConfig;
        }
        
        die('Error: Unable to create widget settings - check folder permissions');
    }
    
    
    public function getInstalledWidgets()
    {        
        $handle = opendir(SERVER_PATH_WIDGETS);
        $folders = array();
        
        // May be wise to create a cache for this...
        while($file = readdir($handle))
        {
            if(is_dir(SERVER_PATH_WIDGETS . '/' . $file) && $file != '.' && $file != '..')
            {
                $configPath = SERVER_PATH_WIDGETS . '/' . $file . '/config.json';
                if(!file_exists($configPath)) continue;
                $config = JSONhelper::JSONFileToArray($configPath);
                $folders[$file] = $config;
                $folders[$file]['_settings_json'] = JSONhelper::arrayToJSON($config['defaults']);
            }
        }
        
        return $folders;
    }
    
    protected function action_updateWidgets($blog) {
           
        $configPath = SERVER_PATH_BLOGS . '/' . $blog['id'] . '/widgets.json';
        if(!file_exists($configPath)) die('Cannot find widget config file');
        $config = rbwebdesigns\JSONhelper::jsonToArray($configPath);
        
        // Clear all existing widgets
        foreach($config as $sectionName => $section) {
            $config[$sectionName] = [];
        }

        foreach($_POST['widgets'] as $sectionName => $section)
        {
            foreach($section as $widgettype => $widgetconfig)
            {
                $config[$sectionName][$widgettype] = json_decode($widgetconfig, true);;
            }
        }
        
        // Save JSON back to config file
        file_put_contents($configPath, rbwebdesigns\JSONhelper::arrayToJSON($config));
        
        // Say it worked
        setSystemMessage(ITEM_UPDATED, "Success");
        
        // View the widgets page
        redirect('/config/' . $blog['id'] . '/widgets');
    }
    
    
    /**
        Save Changes to the display of items in the widget bar
    **/
    public function action_saveWidgetConfig($blog)
    {
        // Get the current widgets as array
        $initJSON = str_replace("&#34;", "\"", $blog['widgetJSON']);
        $arrayWidgetConfig = json_decode($initJSON, true);
        
        // Update config from all posted data
        // Widget POST variables are in format:
        // widgetfld_[widgetname]_[settingname]
        
        // Get the location of the widget
        if(array_key_exists("sys_widget_location", $_POST))
        {
            $widgetlocation = sanitize_string($_POST['sys_widget_location']);
        }
        else die("No location found");
        
        // Get the id of the widget
        if(array_key_exists("sys_widget_id", $_POST))
        {
            $widgetid = sanitize_string($_POST['sys_widget_id']);
        }
        else die("No id found");
        
        // Get the type of the widget
        if(array_key_exists("sys_widget_type", $_POST))
        {
            $widgettype = sanitize_string($_POST['sys_widget_type']);
        }
        else die("No type found");
                
        // Create array for location if needed
        if(!array_key_exists($widgetlocation, $arrayWidgetConfig))
        {
            $arrayWidgetConfig[$widgetlocation] = array();
        }
        
        // Find match in current settings for id
        $targetwidget = -1;
        
        for($i = 0; $i < count($arrayWidgetConfig[$widgetlocation]); $i++)
        {
            if($arrayWidgetConfig[$widgetlocation][$i]['id'] == $widgetid)
            {
                $targetwidget = $i;
                break;
            }
        }
        
        // Create new if needed...
        if($targetwidget == -1)
        {
            // Get the index
            $targetwidget = count($arrayWidgetConfig[$widgetlocation]);
            
            $arrayWidgetConfig[$widgetlocation][] = array(
                'id' => $widgetid,
                'type' => $widgettype
            );
        }
        
        foreach($_POST as $key => $postvariable)
        {
            // ...
            if(substr($key, 0, 9) == "widgetfld")
            {
                $splitkey = explode('_', $key);
                if(count($splitkey) < 3) continue;
                $arrayWidgetConfig[$widgetlocation][$targetwidget][$splitkey[2]] = $postvariable;
            }
        }
        
        // Convert back to JSON string
        $jsonWidgetConfig = json_encode($arrayWidgetConfig);
        
        // Save to database
        $this->modelBlogs->updateWidgetJSON($jsonWidgetConfig, $blog['id']);

        // Return the updated widget config
        // Update UI - don't - this is called using ajax
        // die($jsonWidgetConfig);
        die(json_encode($arrayWidgetConfig[$widgetlocation][$targetwidget]));
        
        // setSystemMessage(ITEM_UPDATED, "Success");
        // redirect(CLIENT_ROOT_BLOGCMS.'/config/'.$blog['id'].'/widgets');
    }
    
    function action_saveWidgetLayout($blog)
    {        
        if(!array_key_exists('fld_submit', $_POST)) die('Submit field missing');
        if(!array_key_exists('widgets', $_POST)) {
            // no widgets...
            $jsonWidgetConfig = "{}";
        }
        else {
            $jsonWidgetConfig = "{";
            $first = true;
            
            foreach($_POST['widgets'] as $widgetlocation => $widgets)
            {
                if(!$first) $jsonWidgetConfig.= ",";
                $jsonWidgetConfig.= "&#34;$widgetlocation&#34;: [";
                
                $first2 = true;
                foreach($widgets as $id => $widgetjson)
                {
                    if(!$first2) $jsonWidgetConfig.= ",";
                    $jsonWidgetConfig.= sanitize_string($widgetjson);
                    $first2 = false;
                }            
                $jsonWidgetConfig.= "]";
                $first = false;
            }
            $jsonWidgetConfig.= "}";
        }
        
        $this->modelBlogs->updateWidgetJSON($jsonWidgetConfig, $blog['id']);
        
        setSystemMessage(ITEM_UPDATED, "Success");
        redirect('/config/'.$blog['id'].'/widgets');
    }
}
