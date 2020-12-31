<?php
namespace rbwebdesigns\HamletCMS\FavouriteBlogs\model;

use rbwebdesigns\core\model\RBFactory;

/**
 * Provides access to the favourites database table.
 *
 * @author Ricky Bertram <ricky@rbwebdesigns.co.uk>
 */
class FavouritePosts extends RBFactory
{
  /**
   * Instantiate the Factory passing in access to the database.
   *
   * @param \rbwebdesigns\core\model\ModelManager $modelManager
   */
  function __construct($modelManager)
  {
    parent::__construct($modelManager);

    $this->tableName = 'favouriteposts';
    $this->fields = [
      'user_id' => 'number',
      'post_id' => 'number'
    ];
  }

  /**
   * Add a post to a users favourites list
   *
   * @param int $userID
   * @param int $postID
   *
   * @return bool
   */
  public function addFavourite($userID, $postID)
  {
    if ($this->isFavourite($userID, $postID)) {
      return false;
    }

    return $this->db->insertRow($this->tableName, [
      'user_id' => $userID,
      'post_id' => $postID,
    ]);
  }

  /**
   * Remove a post from a users favourites list
   *
   * @param int $userID
   * @param int $postID
   *
   * @return bool
   */
  public function removeFavourite($userID, $postID)
  {
    if (!$this->isFavourite($userID, $postID)) {
      return "Blog is not in your favorites list";
    }

    return $this->db->deleteRow($this->tableName, [
      'user_id' => $userID,
      'post_id' => $postID,
    ]);
  }

  /**
   * Check if a post is already in users favourites
   *
   * @param int $userID
   * @param int $postID
   *
   * @return bool
   */
  public function isFavourite($userID, $postID)
  {
    $rowCount = $this->db->countRows($this->tableName, [
      'user_id' => $userID,
      'post_id' => $postID,
    ]);

    return $rowCount > 0;
  }

  /**
   * Get all the favourite posts for a user
   */
  public function getAllFavourites($userID)
  {
    return $this->db->selectMultipleRows($this->tableName, '*', [
      'user_id' => $userID
    ]);
  }

  /**
   * Get all the users that have favoured post
   */
  public function getFavouritees($postID)
  {
    return $this->db->selectMultipleRows($this->tableName, '*', [
      'post_id' => $postID
    ]);
  }

  /**
   * Get the most favoured blogs
   */
  public function getTopFavourites($limit = 10)
  {
    $statement = $this->db->query("SELECT post_id, count(*)
            FROM {$this->tableName}
            GROUP BY post_id
            LIMIT {$limit}
        ");
    return $statement->fetchAll(\PDO::FETCH_ASSOC);
  }

}
