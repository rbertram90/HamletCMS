<?php
namespace rbwebdesigns\blogcms\model;

use rbwebdesigns\blogcms\BlogCMS;
use rbwebdesigns\core\Sanitize;
use rbwebdesigns\core\model\RBFactory;

/**
 * /app/model/mdl_blog.php
 * Access to the blogs database is done through this class
 */
class Blogs extends RBFactory
{
    protected $db;
    protected $tableName;
    private $tblbloguser, $dbfields;

    function __construct($modelManager)
    {
        $this->db = $modelManager->getDatabaseConnection();
        $this->tableName = TBL_BLOGS;
        $this->tblfavourites = TBL_FAVOURITES;
        $this->tblcontributors = TBL_CONTRIBUTORS;
        $this->fields = array(
            'id'          => 'number',
            'name'        => 'string',
            'description' => 'string',
            'user_id'     => 'number',
            'anon_search' => 'boolean',
            'visibility'  => 'string',
            'widgetJSON'  => 'string',
            'pagelist'    => 'string',
            'category'    => 'string'
        );
    }
    
    /**
     *  Get all information stored for a blog
     *  @param int ID for Blog
     *  @return array
    **/
    public function getBlogById($blogid)
    {
        return $this->db->selectSingleRow($this->tableName, array_keys($this->fields), array('id' => $blogid));
    }
    
    /**
     *  Get all the blogs created by a user
     *  @param int ID for User
     *  @return array Blogs for which a user contributes
    **/
    public function getBlogsByUser($intUserid)
    {
        return $this->db->selectMultipleRows($this->tableName, '*', array('user_id' => $intUserid));
    }
    
    /**
     *  Get public blogs starting with @param0
     *  @param string Starting letter
     *  @return array Matching Blogs
    **/
    public function getBlogsByLetter($letter)
    {
        if(!ctype_alpha($letter)) $letter = "[^A-Za-z]"; // search all numbers at once
        $qs = 'SELECT * FROM '.$this->tableName.' WHERE LEFT(name, 1) REGEXP "'.$letter.'" and visibility="anon"';
        return $this->db->select_multi($qs);
    }
    
    /**
     *  Get count of number of public blogs for each letter - explore page
     *  IMPROVED! 20 JULY 2014 - Now uses 1 query rather than 27 to get data by making
     *  the most of 'GROUP BY'!
     *  @return <array> Counts of blogs by letter
     */
    public function countBlogsByLetter()
    {
        $res = array('0' => 0);
        foreach(range('A', 'Z') as $letter) $res[$letter] = 0;
        
        $sql = 'SELECT UCASE(LEFT(name, 1)) as letter, count(*) as count FROM '.$this->tableName.' Where visibility = "anon" Group By UCASE(LEFT(name, 1))';
        $results = $this->db->select_multi($sql);
        
        foreach($results as $value) {
            if(ctype_alpha($value['letter'])) {
                // Letter
                $res[$value['letter']] = $value['count'];
            }
            else {
                // Number (or other)
                $res['0'] += 0 + $value['count'];
            }
        }
        return $res;
    }
    
    /**
     *  Get number of blogs a user contributes to
     *  @param <int>
     */
    public function countBlogsByUser($userid)
    {
        $count = $this->db->selectSingleRow($this->tableName, 'count(*) as blogcount', array('user_id' => $userid));
        return $count['blogcount'];
    }
    
    
    /**
     *  Create a new blog id number (random)
     *  @return string 10 digit string
     */
    private function generateBlogKey()
    {
        // Generate a new random key
        $blog_key = ''.rand(10000,32000).rand(10000,32000);
        
        // Check if this key is unique
        $lbKeyExists = $this->blogKeyExists($blog_key);
        
        // Check to see if generated key already exists!
        if($lbKeyExists) return $this->generateBlogKey();
        else return $blog_key;
    }
    
    
    /**
     * Check if a blog key already exists in the database
     * @param string
     *   10 Digit Blog ID
     * @return boolean
     *   True if found, False Otherwise
     */
    private function blogKeyExists($key)
    {
        $query_string = 'SELECT count(*) as keycount FROM '.$this->tableName.' WHERE id='.Sanitize::int($key);
        
        $result = $this->db->select_single($query_string);
        
        return ($result['keycount'] > 0);
    }
    
    
    /**
     * Create a new blog
     * 
     * @param string
     *   name for the blog
     * @param string
     *   description for the blog
     * @param string
     *   key for the blog (optional)
     * 
     * @return string
     *   key used for the blog
     */
    public function createBlog($pname, $pdesc, $pkey='')
    {
        $blog_name = Sanitize::string($pname);
        $blog_desc = Sanitize::string($pdesc);
        
        if(strlen($pkey) == 0) {
            // Generate a new blog key
            $blog_key = $this->generateBlogKey();
            
            // Check we are creating in the right place
            if (!file_exists(SERVER_PATH_BLOGS)) die(showError(FILE_NOT_FOUND.': /blogs'));
            // Create Folder
            if (!mkdir(SERVER_PATH_BLOGS.'/'.$blog_key, 0777)) die(showError('Failed to create blog folder...'));
            
            // Create Default.php - can we get rid of this as they will all be the same?
            $copy_default = SERVER_PATH_TEMPLATES.'/default/default.php';
            $new_default = SERVER_PATH_BLOGS.'/'.$blog_key.'/default.php';
            if(!copy($copy_default, $new_default)) die(showError("failed to copy $new_default"));
            
            // Create Default.css
            $copy_css = SERVER_PATH_TEMPLATES.'/stylesheets/tmplt_default_blue.css';
            $new_css = SERVER_PATH_BLOGS.'/'.$blog_key.'/default.css';
            if(!copy($copy_css, $new_css)) die(showError("failed to copy $new_css"));
            
            // Create .htaccess
            $copy_htaccess = SERVER_PATH_TEMPLATES.'/default/.htaccess';
            $new_htaccess = SERVER_PATH_BLOGS.'/'.$blog_key.'/.htaccess';
            if(!copy($copy_htaccess, $new_htaccess)) die(showError("failed to copy $new_htaccess"));
            
            // Create default json for blog settings
            $copy_config = SERVER_PATH_TEMPLATES.'/default/config.json';
            $new_config = SERVER_PATH_BLOGS.'/'.$blog_key.'/config.json';
            if(!copy($copy_config, $new_config)) die(showError("failed to copy $new_config"));
            
            // Create default json for blog design settings
            $copy_design = SERVER_PATH_TEMPLATES.'/stylesheets/tmplt_default_blue.json';
            $new_design = SERVER_PATH_BLOGS.'/'.$blog_key.'/template_config.json';
            if(!copy($copy_design, $new_design)) die(showError("failed to copy $new_design"));
        }
        else {
            // Just assigning an exisiting folder into the blog_cms
            // (not something that anyone other than dev would/need want to do)
            $blog_key = Sanitize::int($pkey);
        }
        
        // Insert blog into DB
        $query_string = 'INSERT INTO '.TBL_BLOGS.' (id, name, description, user_id) ';
        $query_string.= "VALUES ('$blog_key','$blog_name','$blog_desc','".$_SESSION['userid']."')";
        
        $this->db->runQuery($query_string);
        
        return $blog_key;
    }
    
    
    /**
     * Delete an existing blog
     */
    public function deleteBlog($blogid)
    {
        // Sanitize Input
        $blogid = safeNumber($blogid);
        // Remove from Database...
        
        // Delete Files and Folders...
        
        // Remove Contributors...
        
        // Remove Posts...
        
        // Remove Comments...
    }
        
    /**
     * Update just the widget configuration JSON for a blog
     */
    public function updateWidgetJSON($psJSON, $psBlogID)
    {
        if(!$this->canWrite($psBlogID)) die("You do not have permission to edit this blog");
        
        // Update Database
        return $this->db->updateRow($this->tableName,
        array(
            'id' => Sanitize::string($psBlogID)
        ),
        array(
            'widgetJSON' => Sanitize::string($psJSON)
        ));
    }
    
    /**
     * Security functions - check Read, Write Permissions
     */
    public function canWrite($blogid) {
        // Only allow contributors to update the blog settings
        // further 'custom restrictions' to be added
        $currentUser = BlogCMS::session()->currentUser;

        $rowCount = $this->db->countRows($this->tblcontributors, array(
            'blog_id' => $blogid,
            'user_id' => $currentUser
        ));
        
        return $rowCount > 0;
    }

    //--------------------------------------------------------
    //    FAVOURITES
    //--------------------------------------------------------
   
    /**
     * Add a blog to a users favourites list
     */
    public function addFavourite($pUserID, $pBlogID)
    {
        if($this->isFavourite($pUserID, $pBlogID)) return "Blog already exists in users favorites list.";  
        $query_string = "INSERT INTO ".$this->tblfavourites." (user_id,blog_id) VALUES ('$pUserID','$pBlogID')";
        $result = $this->db->query($query_string);
        return "Blog Added to Favorites";
    }

    /**
     * Remove a blog to a users favourites list
     */
    public function removeFavourite($pUserID, $pBlogID)
    {
        if(!$this->isFavourite($pUserID, $pBlogID)) return "Blog is not in your favorites list";
        $query_string = "DELETE FROM ".$this->tblfavourites." WHERE user_id='$pUserID' AND blog_id='$pBlogID'";
        $result = $this->db->query($query_string);
        return "Blog Removed from Favorites";
    }

    /**
     * Check if a blog is already a users favourite
     */
    public function isFavourite($pUserID, $pBlogID)
    {
        $arrWhere = array(
            'user_id' => Sanitize::int($pUserID),
            'blog_id' => Sanitize::int($pBlogID)
        );
        $result = $this->db->selectSingleRow($this->tblfavourites, 'count(*) as count', $arrWhere);
        return ($result['count'] != 0);
    }
    
    /**
     * Get all the favourite blogs for a user
     */
    public function getAllFavourites($pUserID)
    {
        $UserID = Sanitize::int($pUserID);
        $query_string = "SELECT a.blog_id, b.* FROM ".$this->tblfavourites." AS a, ".$this->tableName." AS b ";
        $query_string.= "WHERE b.id = a.blog_id AND a.user_id = '$UserID'";
        $statement = $this->db->query($query_string);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Get all the users that have favourited blog
     */
    public function getAllFavouritesByBlog($pBlogID)
    {
        $liBlogID = Sanitize::int($pBlogID);
        $query_string = "SELECT b.* FROM ".$this->tblfavourites." AS a, ".TBL_USERS." as b";
        $query_string.= "WHERE a.user_id = b.id AND a.blog_id = '$liBlogID'";
        $statement = $this->db->query($query_string);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Get counts for the top favourited blogs (ever)
     * - only a matter of time before this becomes a performance issue?
     */
    public function getTopFavourites($num=10, $page=0)
    {
        $query_string = 'SELECT fav.blog_id, count(DISTINCT fav.blog_id) as fav_count, blogs.* ';
        $query_string.= 'FROM '.$this->tblfavourites.' AS fav LEFT JOIN '.$this->tableName.' AS blogs ON fav.blog_id = blogs.id WHERE blogs.visibility = "anon" ';
        $query_string.= 'GROUP BY fav.blog_id ORDER BY fav_count DESC LIMIT '.$page.','.$num;
        $statement = $this->db->query($query_string);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    
    public function getByCategory($category, $num=10, $page=0)
    {
        $query = 'SELECT * ';
        $query.= 'FROM '.$this->tableName.' WHERE category = "' . $category . '" ';
        $query.= 'LIMIT '.$page.','.$num;
        
        $statement = $this->db->query($query);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}
