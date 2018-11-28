<?php
namespace rbwebdesigns\blogcms\Blog\model;

use rbwebdesigns\blogcms\BlogCMS;
use rbwebdesigns\core\Sanitize;
use rbwebdesigns\core\model\RBFactory;

/**
 * Provides access to the blogs database table.
 * 
 * Any simple queries to the blogs table is done through this class the table
 * will likely be accessed elsewhere with complex queries but the common simple
 * ones are handled here.
 * 
 * @package Blog CMS
 * 
 * @author Ricky Bertram <ricky@rbwebdesigns.co.uk>
 */
class Blogs extends RBFactory
{

    /**
     * @var \rbwebdesigns\core\Database Database ORM object
     */
    protected $db;
    /**
     * @var string Database table name this model directly relates to
     */
    protected $tableName;
    /**
     * @var array Keyed with field names and datatype as value to match database definition
     */
    protected $fields;

    /**
     * Instantiate the Factory passing in access to the database.
     * 
     * @param \rbwebdesigns\core\model\ModelManager $modelManager
     */
    function __construct($modelManager)
    {
        $this->db = $modelManager->getDatabaseConnection();
        $this->tableName = TBL_BLOGS;
        $this->tblfavourites = TBL_FAVOURITES;
        $this->tblcontributors = TBL_CONTRIBUTORS;
        $this->subClass = '\\rbwebdesigns\\blogcms\\Blog\\Blog';
        $this->fields = [
            'id'          => 'number',
            'name'        => 'string',
            'description' => 'string',
            'user_id'     => 'number',
            'anon_search' => 'boolean',
            'visibility'  => 'string',
            'widgetJSON'  => 'string',
            'pagelist'    => 'string',
            'category'    => 'string',
            'domain'      => 'string',
        ];
    }
    
    /**
     * Get all information stored for a blog.
     * 
     * @param  int   $blogID  ID of Blog
     * @return array          Query results
     */
    public function getBlogById($blogID)
    {
        return $this->get(array_keys($this->fields), ['id' => $blogID], null, null, false);
    }
    
    /**
     * Get all the blogs created by a user.
     * 
     * @param  int   $userID  Foreign key from users table
     * @return array          Query results
     */
    public function getBlogsByUser($userID)
    {
        return $this->db->selectMultipleRows($this->tableName, '*', ['user_id' => $userID]);
    }
    
    /**
     * Get public blogs starting with $letter.
     * 
     * @param  string  $letter  Starting letter
     * @return array            Blogs that have names that begin with $letter
     */
    public function getBlogsByLetter($letter)
    {
        if(!ctype_alpha($letter)) $letter = "[^A-Za-z]"; // search all numbers at once
        $qs = 'SELECT * FROM '.$this->tableName.' WHERE LEFT(name, 1) REGEXP "'.$letter.'" and visibility="anon"';
        $statement = $this->db->query($qs);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Count the number of public blogs beginning with each letter of the alphabet.
     * 
     * @return array  Counts of blogs by letter
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

    public function countBlogsByCategory()
    {
        return $this->db->query('SELECT category, count(*) FROM blogs WHERE 1 GROUP BY category ORDER BY category ASC');
    }
    
    /**
     *  Get number of blogs a user contributes to.
     * 
     *  @param int $userid
     */
    public function countBlogsByUser($userid)
    {
        return $this->db->count(['user_id' => $userid]);
    }
    
    /**
     * Generates a new blog id number.
     * 
     * @return string
     *  Unique random 10 digit string
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
     * Check if a blog key already exists in the database.
     * 
     * @param string $key
     *  10 Digit Blog ID
     * 
     * @return boolean
     *  True if found, False Otherwise
     */
    private function blogKeyExists($key)
    {
        return $this->count(['id' => $key]);
    }
    
    /**
     * Create a new blog
     * 
     * @param  string  $name  name for the blog
     * @param  string  $desc  description for the blog
     * @param  string  $key   key for the blog (optional)
     * @return string         key used for the blog
     */
    public function createBlog($name, $desc, $key='')
    {
        if(strlen($key) == 0) {
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
            $blog_key = Sanitize::int($key);
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
     * Update just the widget configuration JSON for a blog.
     * 
     * @param string $config
     * 
     * @param int $blogID
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
     * 
     * @param int $blogID
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
