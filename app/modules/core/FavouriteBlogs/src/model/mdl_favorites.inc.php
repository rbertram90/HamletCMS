<?php
namespace rbwebdesigns\HamletCMS\model;

use rbwebdesigns\HamletCMS\HamletCMS;
use rbwebdesigns\core\Sanitize;
use rbwebdesigns\core\model\RBFactory;

/**
 * Provides access to the favourites database table.
 * 
 * @todo Rewrite the whole thing!!!
 * 
 * @author Ricky Bertram <ricky@rbwebdesigns.co.uk>
 */
class Favourites extends RBFactory
{
    
    /**
     * Instantiate the Factory passing in access to the database.
     * 
     * @param \rbwebdesigns\core\model\ModelManager $modelManager
     */
    function __construct($modelManager)
    {
        $this->db = $modelManager->getDatabaseConnection();
        $this->tableName = TBL_FAVOURITES;
        $this->fields = [
            'user_id' => 'number',
            'blog_id' => 'string'
        ];
    }

    /**
     * Add a blog to a users favourites list
     * 
     * @param int $userID
     * @param int $blogID
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
    
}
