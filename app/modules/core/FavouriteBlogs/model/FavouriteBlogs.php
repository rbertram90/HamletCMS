<?php
namespace HamletCMS\FavouriteBlogs\model;

use rbwebdesigns\core\model\RBFactory;

/**
 * Provides access to the favourites database table.
 *
 * @author Ricky Bertram <ricky@rbwebdesigns.co.uk>
 */
class FavouriteBlogs extends RBFactory
{
    /** @var \rbwebdesigns\core\ObjectDatabase */
    protected $db;

    /** @var string Class alias for Hamlet model map */
    public static $alias = 'favouriteblogs';

    /**
     * Instantiate the Factory passing in access to the database.
     * 
     * @param \rbwebdesigns\core\model\ModelManager $modelManager
     */
    function __construct($modelManager)
    {
        parent::__construct($modelManager);

        $this->tableName = 'favouriteblogs';
        $this->fields = [
            'user_id' => 'number',
            'blog_id' => 'number'
        ];
        $this->subClass = '\\HamletCMS\\FavouriteBlogs\\FavouriteBlog';
    }

    /**
     * Add a blog to a users favourites list
     * 
     * @param int $userID
     * @param int $blogID
     *
     * @return bool
     */
    public function addFavourite($userID, $blogID)
    {
        if ($this->isFavourite($userID, $blogID)) {
            return false;
        }

        return $this->db->insertRow($this->tableName, [
            'user_id' => $userID,
            'blog_id' => $blogID,
        ]);
    }

    /**
     * Remove a blog from a users favourites list
     *
     * @param int $userID
     * @param int $blogID
     *
     * @return bool
     */
    public function removeFavourite($userID, $blogID)
    {
        if (!$this->isFavourite($userID, $blogID)) {
            return "Blog is not in your favorites list";
        }

        return $this->db->deleteRow($this->tableName, [
            'user_id' => $userID,
            'blog_id' => $blogID,
        ]);
    }

    /**
     * Check if a blog is already a users favourite
     *
     * @param int $userID
     * @param int $blogID
     *
     * @return bool
     */
    public function isFavourite($userID, $blogID)
    {
        $rowCount = $this->db->countRows($this->tableName, [
            'user_id' => $userID,
            'blog_id' => $blogID,
        ]);

        return $rowCount > 0;
    }
    
    /**
     * Get all the favourite blogs for a user
     * 
     * @return \HamletCMS\FavouriteBlogs\FavouriteBlog[]
     */
    public function getAllFavourites($userID)
    {
        return $this->db->selectMultipleObjects($this->subClass, $this->tableName, '*', [
            'user_id' => $userID
        ]);
    }
    
    /**
     * Get all the users that have favoured blog
     */
    public function getFavouritees($blogID)
    {
        return $this->db->selectMultipleRows($this->tableName, '*', [
            'blog_id' => $blogID
        ]);
    }

    /**
     * Get the most favoured blogs
     */
    public function getTopFavourites($limit = 10)
    {
        $statement = $this->db->query("SELECT blog_id, count(*)
            FROM {$this->tableName}
            GROUP BY blog_id
            LIMIT {$limit}
        ");
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    
}
