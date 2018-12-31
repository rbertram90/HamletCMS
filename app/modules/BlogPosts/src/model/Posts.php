<?php
namespace rbwebdesigns\blogcms\BlogPosts\model;

use rbwebdesigns\blogcms\BlogCMS;
use rbwebdesigns\core\model\RBFactory;
use rbwebdesigns\core\Sanitize;

/**
 * Posts factory class
 * Access to the posts database table is done through this class
 */
class Posts extends RBFactory
{
    /** @var \rbwebdesigns\core\Database $db */
    protected $db;
    /** @var string $tableName */
    protected $tableName;
    /** @var string $subClass */
    protected $subClass;

    /**
     * Posts factory constructor
     * 
     * @param \rbwebdesigns\core\ModelManager $modelManager
     */
    function __construct($modelManager)
    {
        // Access to the database class
        $this->db = $modelManager->getDatabaseConnection();
        
        // Set table names
        $this->tableName = TBL_POSTS;
        $this->tblviews = TBL_POST_VIEWS;
        $this->tblcontributors = TBL_CONTRIBUTORS;
        $this->tblautosave = TBL_AUTOSAVES;
        $this->subClass = '\\rbwebdesigns\\blogcms\\BlogPosts\\Post';
        
        $this->fields = [
            'id'                => 'number',
            'title'             => 'string',
            'summary'           => 'memo',
            'content'           => 'memo',
            'blog_id'           => 'number',
            'link'              => 'string',
            'draft'             => 'boolean',
            'timestamp'         => 'datetime',
            'tags'              => 'string',
            'author_id'         => 'number',
            'type'              => 'string',
            'initialautosave'   => 'boolean'
        ];
    }
    
    /**
     * Get all available information on a single blog post
     * 
     * @param int $postID
     * 
     * @return bool|\rbwebdesigns\blogcms\BlogPosts\Post
     */
    public function getPostById($postID)
    {
        return $this->get('*', ['id' => $postID], null, null, false);
    }
    
    /**
     * Get information on a post by sepcifying the url and blog id
     * 
     * @param string $link
     *   Url path of the post
     * @param int $blogID
     * 
     * @return bool|\rbwebdesigns\blogcms\BlogPosts\Post
     */
    public function getPostByURL($link, $blogID)
    {
        return $this->get('*', ['link' => $link, 'blog_id' => $blogID], null, null, false);
    }
    
    /**
     * Get the lastest (published) blog post
     * 
     * @param int $blogid
     * 
     * @return bool|\rbwebdesigns\blogcms\BlogPosts\Post
     */
    public function getLatestPost($blogID)
    {
        $where = [
            'blog_id'   => $blogID,
            'timestamp' => '< CURRENT_TIMESTAMP',
            'draft'     => 0
        ];
        return $this->get('*', $where, 'timestamp DESC', '1', false);
    }
    
    /**
     * Get the next (published) post in chronological order
     * 
     * @param int $blogID
     * @param string $currentPostTimestamp
     * 
     * @return bool|\rbwebdesigns\blogcms\BlogPosts\Post
     */
    public function getNextPost($blogID, $currentPostTimestamp)
    {
        $where = [
            'timestamp' => '>' . $currentPostTimestamp,
            'blog_id'   => $blogID,
            'draft'     => 0
        ];
        $result = $this->get('*', $where, 'timestamp ASC', '1', false);
        
        // Only Return Result if the post is not scheduled
        if ($result->timestamp < date('Y-m-d H:i:s')) return $result;
    }
    
    /**
     * Get the previous (published) post in chronological order
     * 
     * @param int $blogID
     * @param string $currentPostTimestamp
     * 
     * @return bool|\rbwebdesigns\blogcms\BlogPosts\Post
     */
    public function getPreviousPost($blogID, $currentPostTimestamp)
    {
        $where = [
            'blog_id'   => $blogID,
            'timestamp' => '<'. $currentPostTimestamp,
            'draft'     => 0
        ];
        return $this->get('*', $where, 'timestamp DESC', '1', false);
    }
    
    /**
     * Get (published) posts where the title or tags contain a free text string
     * 
     * @param int $blogID
     * @param string $searchterm
     */
    public function search($blogID, $searchterm)
    {
        // Search posts by title & tags
        $query_string = "SELECT * FROM {$this->tableName} ";
        $query_string.= "WHERE blog_id='{$blogID}' ";
        $query_string.= "AND (title LIKE '%".Sanitize::string($searchterm)."%' OR tags LIKE '%".Sanitize::string($searchterm)."%') ";
        $query_string.= "AND draft=0 AND timestamp <= CURRENT_TIMESTAMP";

        $statement = $this->db->query($query_string);
        return $statement->fetchAll(\PDO::FETCH_CLASS, $this->subClass);
    }
    
    /**
     * Count the number of posts on a blog
     * 
     * @param int     $blogID
     * @param boolean $incDrafts
     * @param boolean $incfutures
     */
    public function countPostsOnBlog($blogID, $incDrafts = false, $incfutures = false)
    {
        $where = ['blog_id' => $blogID];
        if (!$incfutures) $where['timestamp'] = '<' . date('Y-m-d H:i:s');
        if (!$incDrafts) $where['draft'] = 0;

        return $this->count($where);
    }
    
    /**
     * Get a count of posts & the last post date for all contributors for a blog
     * 
     * @return bool|\rbwebdesigns\blogcms\BlogPosts\Post[]
     */
    public function getPostCountByUser($blogID)
    {
        $query_string = "SELECT author_id, count(*) as post_count, 
        (
            SELECT `timestamp`
            FROM {$this->tableName} as b
            WHERE blog_id='{$blogID}'
            AND author_id=a.author_id
            AND `timestamp` < CURRENT_TIMESTAMP
            ORDER BY `timestamp` DESC
            LIMIT 1
        ) as last_post
        FROM {$this->tableName} as a
        WHERE blog_id = '{$blogID}'
        AND draft = 0
        AND `timestamp` <= '". date('Y-m-d H:i:s') ."'
        GROUP BY author_id
        ORDER BY `timestamp` DESC";
        
        $statement = $this->db->query($query_string);
        return $statement->fetchAll(\PDO::FETCH_CLASS, $this->subClass);
    }
    
    /**
     * Get all the posts on a blog
     * @param boolean $drafts - include draft posts or just live ones
     * @param int $blogID - ID number of the blog
     * 
     * @deprecated getPostsByBlog is better?
     */
    public function getAllPostsOnBlog($blogID, $drafts=0, $future=0)
    {
        $tc = TBL_COMMENTS;
        $sql = "SELECT p.* ";
        $sql.= "FROM " . TBL_POSTS . " as p ";
        $sql.= "WHERE p.blog_id = '".$blogID."' ";
        if ($drafts == 0) $sql.= "AND p.draft='0' ";
        if ($future == 0) $sql.= "AND p.timestamp<='".date('Y-m-d H:i:s')."' ";
        $sql.= "ORDER BY p.timestamp DESC ";

        $statement = $this->db->query($sql);
        return $statement->fetchAll(\PDO::FETCH_CLASS, $this->subClass);
    }
    
    /**
     * Get posts by blog including number of comments for each post
     * 
     * @param int $blogID
     * @param int $page
     *   Page number
     * @param int $num
     *   Number of posts to fetch
     * @param boolean $drafts
     *   Should results include unpublished posts
     * @param boolean $future
     *   Should results include scheduled posts
     * @param string $sort
     *   How should the posts be ordered e.g. "title ASC"
     */
    public function getPostsByBlog($blogID, $page=1, $num=10, $drafts=0, $future=0, $sort='')
    {
        $start = ($page-1) * $num;
        $tp = TBL_POSTS; $tc = TBL_COMMENTS; $tv = TBL_POST_VIEWS; $tu = TBL_USERS;
        $sql = "SELECT $tp.*, wordcount($tp.content) as wordcount, (SELECT count(*) FROM $tv WHERE $tv.postid = $tp.id) as uniqueviews, (SELECT COALESCE(SUM(userviews),0) from $tv WHERE $tv.postid = $tp.id) as hits, (SELECT username FROM $tu WHERE id = $tp.author_id) as username ";
        $sql.= "FROM $tp ";
        // (SELECT count(*) from $tc WHERE $tc.post_id = $tp.id) as numcomments
        $sql.= "WHERE $tp.blog_id = '".$blogID."' ";
        if($drafts == 0) $sql.= "AND $tp.draft='0' ";
        if($future == 0) $sql.= "AND $tp.timestamp<='".date('Y-m-d H:i:s')."' ";
        
        $fields = array_merge($this->fields, ['uniqueviews' => 'number', 'hits' => 'number']); // 'numcomments' => 'number', 
        
        $splitSort = explode(' ', $sort);
        if(count($splitSort) == 2) {
            if(array_key_exists($splitSort[0], $fields)) {
                   if(strtoupper($splitSort[1]) != 'ASC') {
                       $splitSort[1] = 'DESC';
                   }
            } else {
                $sort = ''; // Invalid sort
            }
        } else $sort = ''; // Invalid sort
                
        if($sort == '') {
            $sql.= "ORDER BY $tp.timestamp DESC ";
        }
        else {
            $sql.= "ORDER BY $splitSort[0] $splitSort[1] ";
        }
        
        $sql.= "LIMIT $start,$num";
        $statement = $this->db->query($sql);
        return $statement->fetchAll(\PDO::FETCH_CLASS, $this->subClass);
    }
        
    /**
     * Get Recent Posts - Get posts made within the last X days for either a single or an array of blogs
     * 
     * @param  int $pBlogID
     *   id number of the blog to get posts for / array of blog id's
     * @param  date $daysSincePostLimit
     *   default 5 = get posts within the last 5 days
     * 
     * @return array
     *   list of posts that were made since $daysSincePostLimit
     */
    public function getRecentPosts($blogs, $daysSincePostLimit=7)
    {
        $query_string = 'SELECT '.$this->tableName.'.*, '.TBL_BLOGS.'.name as blog_name FROM '.$this->tableName.' LEFT JOIN '.TBL_BLOGS.' ON '.$this->tableName.'.blog_id = '.TBL_BLOGS.'.id WHERE '.$this->tableName.'.timestamp >= DATE_SUB(NOW(), INTERVAL '.$daysSincePostLimit.' DAY) AND timestamp<="'.date('Y-m-d H:i:s').'" AND '.$this->tableName.'.draft = 0 ';

        if(gettype($blogs) == "array") {
            if(count($blogs) == 0) return false;
            foreach($blogs as $key => $blog) {                
                if($key == 0) $query_string.=  " AND (";
                else $query_string.= " OR ";
                $query_string.= $this->tableName.".blog_id='".$blog['blog_id']."'";
            }
            $query_string.= ")";
        }
        elseif(gettype($blogs) == "integer") {
            $query_string.= 'AND '.$this->tableName.'.blog_id="' . Sanitize::int($blogs) . '"';
            
        }
        else return false;
        
        $query_string.= " ORDER BY ".$this->tableName.".timestamp DESC LIMIT 30";
        $statement = $this->db->query($query_string);
        return $statement->fetchAll(\PDO::FETCH_CLASS, $this->subClass);
    }
    
    /**
     * Create a new post
     * @param array $newValues
     */
    public function createPost($newValues)
    {
        $currentUser = BlogCMS::session()->currentUser;
        
        if(!array_key_exists('title', $newValues) || !array_key_exists('content', $newValues)) {
            return false;
        }
        
        if(!array_key_exists('draft', $newValues)) $newValues['draft'] = 0;
        if(!array_key_exists('type', $newValues)) $newValues['type'] = 'standard';

        $newValues['timestamp'] = date("Y-m-d H:i:s");
        $newValues['link'] = $this->createSafePostUrl($newValues['title']);
        $newValues['tags'] = $this->createSafeTagList($newValues['tags']);
        $newValues['author_id'] = $currentUser['id'];
        
        return $this->insert($newValues);
    }
    
    /**
     * Clone a post
     */
    public function clonePost($postID)
    {
        $now = new \DateTime();
        $tempTableName = 'clonepost_'. $now->format('u');

        // Simple SQL row copy
        $query = $this->db->query("CREATE TEMPORARY TABLE `{$tempTableName}` SELECT * FROM {$this->tableName} WHERE id = {$postID}");
        if (!$query) return false;
        $query = $this->db->query("UPDATE `{$tempTableName}` SET id = NULL");
        if (!$query) return false;
        $query = $this->db->query("INSERT INTO {$this->tableName} SELECT * FROM `{$tempTableName}`");
        $newPostID = $this->db->getLastInsertID();
        if (!$query) return false;
        $query = $this->db->query("DROP TEMPORARY TABLE IF EXISTS `{$tempTableName}`");
        if (!$query) return false;

        $newPost = $this->getPostById($newPostID);

        $update = $this->update(['id' => $newPostID], [
            'link' => $newPost->link .'-'. $now->format('u'),
            'draft' => 1,
            'timestamp' => date('Y-m-d H:i:s'),
            'author_id' => BlogCMS::session()->currentUser['id']
        ]);

        return $newPostID;
    }

    /**
     * Update a blog post
     * @param int $postid
     * @param array $newValues
     */
    public function updatePost($postid, $newValues)
    {
        $postid = Sanitize::int($postid);
        $newValues['link'] = $this->createSafePostUrl($newValues['title']);
        
        if(array_key_exists('tags', $newValues))
          $newValues['tags'] = $this->createSafeTagList($newValues['tags']);
        
        return $this->update(['id' => $postid], $newValues);
    }
    
    /**
     * Create Safe Tag List makes sure all tags in a string are 'valid'
     * i.e. don't exist more than once, don't contain funny characters
     * and deals with spaces.
     * 
     * @param string $tags
     *   Comma seperated tag list
     * @return string
     *   CSV of formatted, valid tags
     */
    public static function createSafeTagList($tags)
    {
        $splitCSV = explode(",", $tags);
        $validTagsString = "";
        $validTagsArray = Array();
        
        foreach ($splitCSV as $tag) {
            $validTag = trim($tag);
            // Remove anything that isn't alphanumeric or a space
            $validTag = preg_replace("/[^A-Za-z0-9 ]/", '', $validTag);
            $validTag = str_replace(" ", "+", $validTag);
            // Check the entry doesn't already exists
            if (array_search($validTag, $validTagsArray) === false) {
                // Append to new CSV
                if(strlen($validTagsString) > 0) $validTagsString.= ",".$validTag;
                else $validTagsString.= $validTag;
                // Add to array also
                array_push($validTagsArray, $validTag);
            }
        }
        
        return $validTagsString;
    }
    
    /**
     * Create a safe URL for a post (no funny characters)
     * @param <string> $text - string to use for URL
     * @return <string> a safe URL to make the pages more SEO friendly
     */
    public static function createSafePostUrl($text)
    {
        // Remove anything that isn't alphanumeric or a space
        $postlink = preg_replace("/[^A-Za-z0-9 ]/", '', $text);
        return strtolower(str_replace(" ", "-", Sanitize::string($postlink)));
    }
    
    /**
     * Get counts for all tags on a blog
     * @param <int> $blogid - blog to get counts
     * @return <array> of tuples - array[tagname,count]
     */
    public function countAllTagsByBlog($blogid, $sortby='text')
    {
        // Get the posts from this blog
        $posts = $this->getAllPostsOnBlog($blogid);
        $res = array();
        
        // Loop through the posts
        foreach ($posts as $post) {
            // Create array from CSV string
            $tags = explode(",", $post->tags);
            // Check this post has tags
            if (count($tags) === 0) continue;
            // Loop through tags
            foreach ($tags as $tag) {
                $tag = trim($tag);
                // Check tag is not empty
                if(strlen($tag) === 0) continue;
                // Is this tag already part of the array?
                $countadded = $this->searchForTag($res, $tag);
                // Increment count depending on if it key already exists
                if($countadded === false) $res[] = [
                    'text' => strtolower($tag),
                    'count' => 1
                ];
                else $res[$countadded]['count'] += 1;
            }
        }
        
        if ($sortby == 'count') {
            sksort($res, $sortby, false);
        }
        else {
            sksort($res, $sortby, true);
        }
        return $res;
    }

    /**
     * Get all unique tags that have been applied
     * to posts for this blog
    **/
    public function getAllTagsByBlog($blogId)
    {
        $posts = $this->getAllPostsOnBlog($blogId);
        $allTags = [];
        
        // Loop through the posts
        foreach ($posts as $post) {
            $tags = explode(",", $post->tags);
            if(count($tags) === 0) continue;
            
            foreach ($tags as $tag) {
                $tag = trim($tag);
                if(strlen($tag) === 0) continue;
                
                // Add to array if not already there
                if(!in_array($tag, $allTags)) $allTags[] = strtolower($tag);
            }
        }
        
        // Sort by name
        sort($allTags);
        
        return $allTags;
    }
    
    /**
     * Check if the tag is in an array
     * @param  string $tag
     * @param  array  $tags
     * @return bool   if the tag exists returns position, false otherwise
     */
    private function searchForTag($tags, $tag)
    {
        for ($i = 0; $i < count($tags); $i++) {
            if($tags[$i]['text'] == strtolower($tag)) return $i;
        }
        return false;
    }
    
    /**
     * Get all the posts on a blog with specified tag (must be exact)
     * @param <int> $blogid - ID number of the blog
     * @param <string> $ptag - Tag
     * @return <array> of posts
     */
    public function getBlogPostsByTag($blogid, $ptag)
    {
        $posts = $this->getAllPostsOnBlog($blogid);
        $res = [];
        
        // Loop through all tags in all posts
        foreach ($posts as $post) {
            $tags = explode(",", $post->tags);
            if(count($tags) == 0) continue;
            
            foreach($tags as $tag) {
                $tag = str_replace("+", " ", $tag);
                // Compare - Case Insensitive
                if(strtolower(trim($tag)) == strtolower(trim($ptag))) $res[] = $post;
            }
        }
        return $res;
    }        
    
    /**
     * Get all IP that have viewed this post
     * @param $postid - id for the post that is viewed
     */
    public function getViewsByPost($postid)
    {
        $postid = Sanitize::int($postid);
        $sql = 'SELECT * FROM '.TBL_POST_VIEWS.' WHERE postid = "'.$postid.'"';
        $statement = $this->db->query($sql);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Increment a specific users' view count (by IP)
     * @param $postid - id for the post that is viewed
     * @param $userip - the user's ip
     */
    public function incrementUserView($postid, $userip)
    {        
        $sql = "UPDATE ".TBL_POST_VIEWS." SET userviews=userviews+1 WHERE postid='$postid' AND userip='$userip'";
        $this->db->query($sql);
    }
    
    /**
     * Add a new user row to the post view table
     * @param $postid - id for the post that is viewed
     * @param $userip - the user's ip
     */
    public function recordUserView($postid, $userip)
    {
        $this->db->insertRow(TBL_POST_VIEWS, [
            'postid'=> $postid,
            'userip' => $userip,
            'userviews' => 1
        ]);
    }
    

}
