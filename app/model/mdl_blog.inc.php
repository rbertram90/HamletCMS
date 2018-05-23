<?php
namespace rbwebdesigns\blogcms\model;

use rbwebdesigns\blogcms\BlogCMS;
use rbwebdesigns\core\Sanitize;
use rbwebdesigns\core\model\RBFactory;

/**
 * /app/model/mdl_blog.php
 * Access to the blogs database is done through this class
 * 
 * @author R Bertram <ricky@rbwebdesigns.co.uk>
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
        $this->fields = [
            'id'          => 'number',
            'name'        => 'string',
            'description' => 'string',
            'user_id'     => 'number',
            'anon_search' => 'boolean',
            'visibility'  => 'string',
            'widgetJSON'  => 'string',
            'pagelist'    => 'string',
            'category'    => 'string'
        ];
    }
    
    /**
     *  Get all information stored for a blog
     *  @param int ID for Blog
     *  @return array
    **/
    public function getBlogById($blogID)
    {
        return $this->db->selectSingleRow($this->tableName, array_keys($this->fields), ['id' => $blogID]);
    }
    
    /**
     *  Get all the blogs created by a user
     *  @param int ID for User
     *  @return array Blogs for which a user contributes
    **/
    public function getBlogsByUser($userID)
    {
        return $this->db->selectMultipleRows($this->tableName, '*', ['user_id' => $userID]);
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
        $statement = $this->db->query($qs);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     *  Get count of number of public blogs for each letter - explore page
     *  @return <array> Counts of blogs by letter
     */
    public function countBlogsByLetter()
    {
        $res = array('0' => 0);
        foreach (range('A', 'Z') as $letter) $res[$letter] = 0;
        
        $sql = 'SELECT UCASE(LEFT(name, 1)) as letter, count(*) as count FROM '.$this->tableName.' Where visibility = "anon" Group By UCASE(LEFT(name, 1))';
        $statement = $this->db->query($sql);
        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);
        
        foreach ($results as $value) {
            if (ctype_alpha($value['letter'])) {
                // Letter
                $res[$value['letter']] = (int) $value['count'];
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
        return $this->db->count(['user_id' => $userid]);
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
        return $this->count(['id' => $key]);
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
    public function createBlog($name, $desc, $pkey='')
    {
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
            $copy_css = SERVER_PATH_TEMPLATES.'/default_blue_2columns_left/stylesheet.css';
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
            $copy_design = SERVER_PATH_TEMPLATES.'/default_blue_2columns_left/config.json';
            $new_design = SERVER_PATH_BLOGS.'/'.$blog_key.'/template_config.json';
            if(!copy($copy_design, $new_design)) die(showError("failed to copy $new_design"));
        }
        else {
            // Just assigning an exisiting folder into the blog_cms
            // (not something that anyone other than dev would/need want to do)
            $blog_key = Sanitize::int($pkey);
        }
        
        $insert = $this->insert([
            'id' => $blog_key,
            'name' => $name,
            'description' => $desc,
            'user_id' => BlogCMS::session()->currentUser['id']
        ]);

        if ($insert == false) return false;
        
        return $blog_key;
    }
    
    /**
     * Update just the widget configuration JSON for a blog
     */
    public function updateWidgetJSON($config, $blogID)
    {
        return $this->db->updateRow($this->tableName, ['id' => $blogID], [
            'widgetJSON' => $config
        ]);
    }
    
    /**
     * Security functions - check Read, Write Permissions
     * Should this be in contributors model?
     */
    public function canWrite($blogID)
    {
        // Only allow contributors to update the blog settings
        // further 'custom restrictions' to be added
        $currentUser = BlogCMS::session()->currentUser;

        $rowCount = $this->db->countRows($this->tblcontributors, array(
            'blog_id' => $blogID,
            'user_id' => $currentUser['id']
        ));
        
        return $rowCount > 0;
    }

    //--------------------------------------------------------
    //    FAVOURITES
    //--------------------------------------------------------
   
    /**
     * Add a blog to a users favourites list
     */
    public function addFavourite($userID, $blogID)
    {
        if($this->isFavourite($userID, $blogID)) return "Blog already exists in users favorites list.";  
        $query_string = "INSERT INTO ".$this->tblfavourites." (user_id,blog_id) VALUES ('$userID','$blogID')";
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
    
    /**
     * Get Blogs by Category
     */
    public function getByCategory($category, $num=10, $page=0)
    {
        $query = 'SELECT * ';
        $query.= 'FROM '.$this->tableName.' WHERE category = "' . $category . '" ';
        $query.= 'LIMIT '.$page.','.$num;
        
        $statement = $this->db->query($query);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
}
