<?php
namespace rbwebdesigns\blogcms;
use rbwebdesigns;

/**********************************************************************
  class SettingsController

  This is the controller which acts as the intermediatory between the
  model (database) and the view. Any requests to the model are sent from
  here rather than the view.

  Example requests that will be handled here:
    /blog_cms/config/2756022810/posts
    /blog_cms/config/2756022810/footer
    /blog_cms/config/2756022810/footer/submit

**********************************************************************/

class SettingsController extends GenericController
{
    // Class Variables
    private $modelBlogs;		// Blogs Model
    private $modelPosts;		// Posts Model
    private $modelComments;		// Comments Model
    private $modelUsers;		// Users Model
    private $modelContributors; // Contributors Model
    private $classSecurity;		// Security Functions
    private $db;
    
    protected $view;
    
    // Constructor
    public function __construct($cms_db, $view)
    {
        // Initialise Models
        $this->modelBlogs           = new ClsBlog($cms_db);
        $this->modelContributors    = new ClsContributors($cms_db);
        $this->modelPosts           = new ClsPost($cms_db);
        $this->modelComments        = new ClsComment($cms_db);
        $this->modelUsers           = $GLOBALS['modelUsers'];
        $this->classSecurity        = new rbwebdesigns\AppSecurity();
        $this->db = $cms_db;
        $this->view = $view;
    }
	
    
    /*
        function: route
        View A Page Under the Blog Settings Section

        @param    DATA    an array of configuration variables to be passed through
                          to the view. This will more than likely always be returned
                          from the function unless it redirects elsewhere.
		
                          structure - array (
                            'page_title'        => <string>,
                            'page_description'  => <string>,
                            'includes_css'      => <array:string>, - file paths relative to the root directory
                            'includes_js'       => <array:string>,
                            'page_content'      => <memo>,
                            'page_menu_actions' => <memo>
                          )
		
        @param    params    Miscellaneous inputs from the URL such as blog id again
                            accessed in an array.
    */
    public function route($params)
    {
        // Deal with arguments!
        $blog_id = key_exists(0, $params) ? safeNumber($params[0]) : '';
        $page_name = key_exists(1, $params) ? sanitize_string($params[1]) : '';
		
        // Check we have permission to perform action
        if(!$this->modelContributors->isBlogContributor($blog_id, $_SESSION['userid'], 'all')) return $this->throwAccessDenied();
        
        // Get blog info
        $blog = $this->modelBlogs->getBlogById($blog_id);
        
        // See if a form has been submitted
        $formsubmitted = (key_exists(2, $params) && strtolower($params[2]) === 'submit');
        
        // Set blog in view
        $this->view->setVar('blog', $blog);
        
        // Route to correct content
        switch($page_name)
        {
            case "general":
            // Name, Description etc.
            if($formsubmitted) return $this->action_updateBlogGeneral($blog); // todo: check
            $this->view->setPageTitle('General Settings - '.$blog['name']);
            $this->view->setVar('categorylist', $GLOBALS['config']['blogcategories']);
            $this->view->render('settings/general.tpl');
            break;

        
            case "posts":
            // Post formatting
            if($formsubmitted) return $this->action_updatePostsSettings($blog);
            $postConfig = $this->getBlogConfig($blog['id']);
            if(isset($postConfig['posts']))
            {
                // Default values where needed
                if(!isset($postConfig['posts']['postsperpage'])) $postConfig['posts']['postsperpage'] = 5;
                if(!isset($postConfig['posts']['postsummarylength'])) $postConfig['posts']['postsummarylength'] = 200;
                $this->view->setVar('postConfig', $postConfig['posts']);
            }
            else
            {
                // No posts key exists - send defaults
                $this->view->setVar('postConfig', array(
                    'postsperpage' => 5,
                    'postsummarylength' => 200
                ));
            }
            $this->view->setPageTitle('Post Settings - '.$blog['name']);
            $this->view->render('settings/posts.tpl');
            break;

        
            case "stylesheet":
            // Manual Edit Stylesheet
            if($formsubmitted) return $this->action_saveStylesheet($params);
            $this->view->setPageTitle('Edit Stylesheet - '.$blog['name']);
            $this->view->render('settings/stylesheet.tpl');
            break;

        
            case "blogdesigner":
            // Blog Style Designer
            if($formsubmitted) return $this->action_updateBlogDisplaySettings($blog);
            $this->view->setPageTitle('Blog Designer - '.$blog['name']);
            $this->view->addScript('/resources/colorpicker/jscolor');
            $this->view->render(SERVER_ROOT.'/app/view/settings/blogdesigner.php', array('blog' => $blog, 'cms_db' => $this->db));
            break;
            
            
            case "template":
            // Template Chooser
            if($formsubmitted) return $this->action_applyNewTemplate($DATA, $params);
            $this->view->setPageTitle('Choose Template - '.$blog['name']);
            $this->view->render('settings/template.tpl');
            break;
            
            
            case "pages":
            // Check actions
            if(key_exists(2, $params))
            {            
                switch(strtolower($params[2]))
                {
                    case 'add'   : $this->action_addPage($blog); break;
                    case 'up'    : $this->action_movePageUp($blog); break;
                    case 'down'  : $this->action_movePageDown($blog); break;
                    case 'remove': $this->action_removePage($blog); break;
                }
            }

            $pagelist = explode(',', $blog['pagelist']);
            $pages = array();
            $taglist = array();

            foreach($pagelist as $postID)
            {
                if(is_numeric($postID)) $pages[] = $this->modelPosts->get('*', array('id' => $postID), '', '', false);
                elseif(substr($postID, 0, 2) == "t:") {
                    $taglist[] = substr($postID, 2);
                    $pages[] = $postID;
                }
            }
            
            $tags = $this->modelPosts->getAllTagsByBlog($blog['id']);

            $this->view->setVar('pagelist', $pagelist); // todo: this is setting a string, should be array...
            $this->view->setVar('pages', $pages);
            $this->view->setVar('taglist', $taglist);
            $this->view->setVar('tags', $tags);
            $this->view->setVar('posts', $this->modelPosts->get(array('id','title'), array('blog_id' => $blog['id'])));
            $this->view->setPageTitle('Manage Pages - '.$blog['name']);
            $this->view->render('settings/pages.tpl');
            break;
            
        
            case "widgets":
			
			// Save the overall widget list
			if($formsubmitted) return $this->action_saveWidgetLayout($blog);
			
            // Check for submission on individual widget config forms
            $subformsubmitted = (key_exists(3, $params) && strtolower($params[3]) === 'submit');
            
            // Check if the form has been submitted
            if($subformsubmitted) return $this->action_saveWidgetConfig($blog);
            
            // Set Page Title
            $this->view->setPageTitle('Customise Widgets - '.$blog['name']);
            
            // Expect name of widget - cannot be 'submit' as already checked!
            // if(array_key_exists(2, $params))
            // {
            //     // specific config found
            //     $widgetname = sanitize_string($params[2]);
            //     $this->viewWidgetSpecificSettings($blog, $widgetname);
            //     break;
            // }
			
            // Convert Quotes
            $BlogJSON = str_replace("&#34;", '"', $blog['widgetJSON']);
            
			// Convert to array
            $BlogJSON = json_decode($BlogJSON, true);
			
			// Check all sections exist in config
			$this->checkWidgetJSON($blog, $BlogJSON);
            
			// echo printArray($BlogJSON);
			
			$this->view->addStylesheet('/resources/css/rbwindow');
			$this->view->addScript('/resources/js/rbwindow');
			
            $this->view->setVar('widgetconfig', $BlogJSON);
            $this->view->render('settings/widgets.tpl');
			
            break;
            
			
            case "header":
            // Change header content
            if($formsubmitted) return $this->action_updateHeaderContent($blog);
            
            // Set the config array
            $blogconfig = $this->getBlogConfig($blog['id']);
            if(isset($blogconfig['header'])) $this->view->setVar('blogconfig', $blogconfig['header']);
            else $this->view->setVar('blogconfig', array());
            $this->view->addScript('/resources/js/rbwindow');
            $this->view->addScript('/resources/js/rbrtf');
            $this->view->addStylesheet('/resources/css/rbrtf');
            $this->view->addStylesheet('/resources/css/rbwindow');
            $this->view->setPageTitle('Customise Blog Header - '.$blog['name']);
            $this->view->render('settings/header.tpl');
            break;
            
            
            case "footer":
            // Change footer content
            if($formsubmitted) return $this->action_updateFooterContent($blog);
            
            // Set the config array
            $blogconfig = $this->getBlogConfig($blog['id']);
            if(isset($blogconfig['footer'])) $this->view->setVar('blogconfig', $blogconfig['footer']);
            else $this->view->setVar('blogconfig', array());
            $this->view->addScript('/resources/js/rbwindow');
            $this->view->addScript('/resources/js/rbrtf');
            $this->view->addStylesheet('/resources/css/rbrtf');
            $this->view->addStylesheet('/resources/css/rbwindow');
            $this->view->setPageTitle('Customise Blog Footer - '.$blog['name']);
            $this->view->render('settings/footer.tpl');
            break;
            

            default:
            // View the settings menu page
            $this->view->setPageTitle('Blog Settings - '.$blog['name']);
            $this->view->render('settings/menu.tpl');
            break;
        }
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
        View a specific widget config page
    **/
    private function viewWidgetSpecificSettings($blog, $widgetname)
    {
        $settings_view = SERVER_ROOT.'/app/view/settings/widgets_'.$widgetname.'.php';
        
        if(file_exists($settings_view)) $this->view->render($settings_view, array('blog' => $blog));
        else $this->throwNotFound();
    }
    
    
	/**
		Update the name and description of a blog
	**/
	public function action_updateBlogGeneral($blog)
    {
        
		// Update Database
		$update = $this->modelBlogs->updateBlog($blog['id'], array(
            'name' => $_POST['fld_blogname'],
            'description' => $_POST['fld_blogdesc'],
            'visibility' => $_POST['fld_blogsecurity'],
            'category' => $_POST['fld_category']
        ));
        
        // System Message
		if($update !== false) setSystemMessage(ITEM_UPDATED, "Success");
		else setSystemMessage($update, "Error");
        
        // Redirect
		redirect('/config/'.$blog['id']);
	}
	
    
	/**
		Update how posts are displayed on the blog
	**/
	public function action_updatePostsSettings($blog)
    {
		// Sanitize Input -> Update DB
        // Lots of inputs!!!!
        $this->updateBlogConfig($blog['id'], array(
			'posts' => array(
				'dateformat'        => safeString($_POST['fld_dateformat']),
				'timeformat'        => safeString($_POST['fld_timeformat']),
				'postsperpage'      => safeNumber($_POST['fld_postsperpage']),
				'allowcomments'     => safeNumber($_POST['fld_commentapprove']),
                'postsummarylength' => safeNumber($_POST['fld_postsummarylength']),
                'showtags'          => safeString($_POST['fld_showtags']),
                'dateprefix'        => safeString($_POST['fld_dateprefix']),
                'dateseperator'     => safeString($_POST['fld_dateseperator']),
                'datelocation'      => safeString($_POST['fld_datelocation']),
                'timelocation'      => safeString($_POST['fld_timelocation']),
                'showsocialicons'   => safeString($_POST['fld_showsocialicons']),
                'shownumcomments'   =>safeString($_POST['fld_shownumcomments'])
			)
		));
		
		// Success / Failure
		setSystemMessage(ITEM_UPDATED, "Success");
        
        // Redirect
		redirect('/config/'.$blog['id']);
	}
    
    
	/**
		Get the blog config file JSON
	**/
	private function getBlogConfig($blogid)
    {
		return jsonToArray(SERVER_PATH_WWW_ROOT.'/blogdata/'.$blogid.'/config.json');
	}
	
    
	/**
		Save to the blog config file
	**/
	private function saveBlogConfig($blogid, $arrBlogConfig)
    {
		$json_string = json_encode($arrBlogConfig);
		file_put_contents(SERVER_PATH_WWW_ROOT.'/blogdata/'.$blogid.'/config.json', $json_string);
	}
	
    
	/**
		Update the blog configuration file with new values
		Note that new arrays are created if needs be.
	**/
	public function updateBlogConfig($pintblogid, $parrayupdates)
    {
	    // Check we have permission to update config file
		if(!$this->modelContributors->isBlogContributor($pintblogid, $_SESSION['userid'])) return $this->throwAccessDenied();
	    // Fetch config from JSON file
		$lobjSettings = $this->getBlogConfig($pintblogid);
		// Apply the changes
		$this->processArray($lobjSettings, $parrayupdates);
		// Save back to JSON file
		$this->saveBlogConfig($pintblogid, $lobjSettings);
		return true;
	}
	private function processArray(&$ptargetarray, $psourcearray)
    {
		foreach($psourcearray as $key => $value)
        {
			if(getType($psourcearray[$key]) == "array")
            {
				if(!array_key_exists($key, $ptargetarray)) $ptargetarray[$key] = array();
				$this->processArray($ptargetarray[$key], $psourcearray[$key]);
			}
			else $ptargetarray[$key] = $value;
		}
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
    
    public function action_addPage($blog)
    {
        // need to check the id does actually correspond to a post
        // which belongs to the blog!
        if(!isset($_POST['fld_pagetype'])) return false;
        
        $pageType = sanitize_string($_POST['fld_pagetype']);
        
        switch($pageType)
        {
            case 'p':
                $newpageID = sanitize_number($_POST['fld_postid']);
                
                // Check the post exists and belongs to this blog
                $targetpost = $this->modelPosts->get(array('id','blog_id'), array('id' => $newpageID), '', '', false);

                if(strtolower(getType($targetpost)) != 'array' || $targetpost['blog_id'] != $blog['id']) die("Unexpected Post ID!");

                break;
                
                
            case 't':
                $tag = str_replace(',', '', sanitize_string($_POST['fld_tag']));
                $newpageID = 't:' . $tag;
                break;
        }

        
        // Update Current Page List
        if(array_key_exists('pagelist', $blog) && strlen($blog['pagelist']) > 0) $pagelist = $blog['pagelist'].','.$newpageID;
        else $pagelist = $newpageID;
        
        $this->modelBlogs->update(array('id' => $blog['id']), array('pagelist' => $pagelist));
        
        // Output
		setSystemMessage(ITEM_CREATED, "Success");
		redirect('/config/'.$blog['id'].'/pages');
    }
	
    
    public function action_removePage($blog)
    {
        // need to check the id does actually correspond to a post
        // which belongs to the blog!
        if(!isset($_POST['fld_postid'])) return false;
        
        if(is_numeric($_POST['fld_postid']))
        {
            $targetPostID = sanitize_number($_POST['fld_postid']);

            // Check the post exists and belongs to this blog
            $targetpost = $this->modelPosts->get(array('id','blog_id'), array('id' => $targetPostID), '', '', false);
            
            if(strtolower(getType($targetpost)) != 'array' || $targetpost['blog_id'] != $blog['id']) die("Unexpected Post ID!");
        }
        else
        {
            $targetPostID = sanitize_string($_POST['fld_postid']);
        }
        
        $pagelist = explode(',', $blog['pagelist']);
        $idKey = array_search($targetPostID, $pagelist);
        
        if($idKey !== false)
        {
            array_splice($pagelist, $idKey, 1);
            
            // Update Current Page List
            $pagelist = implode(',', $pagelist);
                        
            // Update Database
            $this->modelBlogs->update(array('id' => $blog['id']), array('pagelist' => $pagelist));
            
            // Set Message
            setSystemMessage("Page Removed", "Success");
        }
        else
        {
            setSystemMessage("Unable To Remove Page", "Warning");
        }
		redirect('/config/'.$blog['id'].'/pages');
    }
    
    
    public function action_movePageUp($blog)
    {
        // need to check the id does actually correspond to a post
        // which belongs to the blog!
        if(!isset($_POST['fld_postid'])) return false;
        
        if(is_numeric($targetPostID)) $targetPostID = sanitize_number($_POST['fld_postid']);
        else $targetPostID = sanitize_string($_POST['fld_postid']);
        
        $pagelist = explode(',', $blog['pagelist']);
        $idKey = array_search($targetPostID, $pagelist);
        
        if($idKey !== false && $idKey > 0)
        {
            $pagelist[$idKey] = $pagelist[$idKey - 1];
            $pagelist[$idKey - 1] = $targetPostID;
            
            // Update Current Page List
            $pagelist = implode(',', $pagelist);
                        
            // Update Database
            $this->modelBlogs->update(array('id' => $blog['id']), array('pagelist' => $pagelist));
            
            // Set Message
            setSystemMessage("Page Moved Up", "Success");
        }
        else
        {
            setSystemMessage("Unable To Move Page", "Warning");
        }
        
		redirect(CLIENT_ROOT_BLOGCMS.'/config/'.$blog['id'].'/pages');
    }
    
    
    public function action_movePageDown($blog)
    {
        // need to check the id does actually correspond to a post
        // which belongs to the blog!
        if(!isset($_POST['fld_postid'])) return false;
        
        if(is_numeric($targetPostID)) $targetPostID = sanitize_number($_POST['fld_postid']);
        else $targetPostID = sanitize_string($_POST['fld_postid']);
        
        $pagelist = explode(',', $blog['pagelist']);
        $idKey = array_search($targetPostID, $pagelist);
        
        if($idKey !== false && $idKey < count($pagelist)-1)
        {
            $pagelist[$idKey] = $pagelist[$idKey + 1];
            $pagelist[$idKey + 1] = $targetPostID;
            
            // Update Current Page List
            $pagelist = implode(',', $pagelist);
            
            // Update Database
            $this->modelBlogs->update(array('id' => $blog['id']), array('pagelist' => $pagelist));
            
            // Set Message
            setSystemMessage("Page Moved Down", "Success");
        }
        else
        {
            setSystemMessage("Unable To Move Page", "Warning");
        }
		redirect('/config/'.$blog['id'].'/pages');
    }
    
    
	/**
		New - works in reverse of previous version
		This is becuase we want to have values in JSON
		that we don't want to send to the browser
		
		The previous version literally re-generated the
		whole JSON every time the blog designer form
		was submitted...
		
		This one loops through the JSON stored on the
		server and looks for the corresponding value
		in $_POST
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
		// Check we have permission to perform action
		if(!$this->modelContributors->isBlogContributor($blog_key, $_SESSION['userid'])) return $this->throwAccessDenied();
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
        $blog_id = sanitize_number($params[0]);
		// Check we have permission to perform action
		if(!$this->modelContributors->isBlogContributor($blog_id, $_SESSION['userid'])) return $this->throwAccessDenied();
		// Update default.css
        if(is_dir(SERVER_PATH_WWW_ROOT.'/blogdata/'.$blog_id))
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
		if(!array_key_exists('widgets', $_POST))
		{
			// no widgets...
			$jsonWidgetConfig = "{}";
		}
		else
		{
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
		// echo printArray($_POST);
		// echo $jsonWidgetConfig;
	}
}
?>