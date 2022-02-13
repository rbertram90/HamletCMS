<?php
namespace HamletCMS\BlogPosts\model;

use HamletCMS\HamletCMS;
use rbwebdesigns\core\model\RBFactory;
use rbwebdesigns\core\Sanitize;

/**
 * Posts factory class
 * Access to the posts database table is done through this class
 */
class Posts extends RBFactory
{
    /** @var \rbwebdesigns\core\Database */
    protected $db;

    /** @var \rbwebdesigns\core\querybuilder\Database */
    protected $queryBuilder;

    /** @var string */
    protected $subClass;
    
    /** @var string Class alias for Hamlet model map */
    public static $alias = 'posts';

    /**
     * Posts factory constructor
     * 
     * @param \rbwebdesigns\core\ModelManager $modelManager
     */
    function __construct($modelManager)
    {
        // Access to the database class
        $this->db = $modelManager->getDatabaseConnection();

        $config = HamletCMS::config()['database'];

        // New OO-style query builder - experimental!
        $this->queryBuilder = new \rbwebdesigns\core\querybuilder\Database([
            'host' => $config['server'],
            'port' => 3306,
            'name' => $config['name'],
            'user' => $config['user'],
            'password' => $config['password'],
        ]);
        
        // Set table names
        $this->tableName = TBL_POSTS;
        $this->tblviews = TBL_POST_VIEWS;
        $this->tblcontributors = TBL_CONTRIBUTORS;
        $this->tblautosave = TBL_AUTOSAVES;
        $this->subClass = '\\HamletCMS\\BlogPosts\\Post';
        $this->fields = [
            'id'              => 'number',
            'title'           => 'string',
            'summary'         => 'memo',
            'content'         => 'memo',
            'blog_id'         => 'number',
            'link'            => 'string',
            'draft'           => 'boolean',
            'timestamp'       => 'datetime',
            'tags'            => 'string',
            'author_id'       => 'number',
            'type'            => 'string',
            'initialautosave' => 'boolean'
        ];

        // Allow custom modules to add fields to post model
        HamletCMS::runHook('modelSchema', ['model' => $this]);
    }
    
    /**
     * Create a custom query
     * 
     * @return \RBwebdesigns\core\querybuilder\SelectQuery
     */
    public function getQuery() {
        return $this->queryBuilder->select($this->tableName);
    }
    
    /**
     * Run the query and fetch a single post
     * 
     * @param \RBwebdesigns\core\querybuilder\SelectQuery $query
     * 
     * @return \HamletCMS\BlogPosts\Post
     */
    public function getPostFromQuery($query) {
        return $query->execute()->fetchObject($this->subClass);
    }

    /**
     * Run the query and fetch a multiple posts
     * 
     * @param \RBwebdesigns\core\querybuilder\SelectQuery $query
     * 
     * @return \HamletCMS\BlogPosts\Post[]
     */
    public function getPostsFromQuery($query) {
        return $query->execute()->fetchAll(\PDO::FETCH_CLASS, $this->subClass);
    }

    /**
     * Get all available information on a single blog post
     * 
     * @param int $postID
     * 
     * @return \HamletCMS\BlogPosts\Post|bool
     */
    public function getPostById($postID)
    {
        return $this->getPostFromQuery(
            $this->getQuery()->condition('id', $postID)
        );
    }
    
    /**
     * Get information on a post by sepcifying the url and blog id
     * 
     * @param string $link
     *   Url path of the post
     * @param int $blogID
     * 
     * @return bool|\HamletCMS\BlogPosts\Post
     */
    public function getPostByURL($link, $blogID)
    {
        $query = $this->getQuery()
            ->condition('link', $link)
            ->condition('blog_id', $blogID);
        return $this->getPostFromQuery($query);
    }

    /**
     * Get all posts for a blog made by a user
     * 
     * @param int $blogID       Blog ID
     * @param int $authorID     User ID
     * @param int $limit        Number of posts to get
     * @param int $page         Page offset
     * @param boolean $drafts   Include draft posts?
     * 
     * @return \HamletCMS\BlogPosts\Post[]|bool
     */
    public function getPostsByAuthor($blogID, $authorID, $limit=10, $page=1, $drafts=0)
    {
        $offset = ($page-1) * $limit;

        $query = $this->queryBuilder->select($this->tableName)
          ->condition('author_id', $authorID)
          ->condition('blog_id', $blogID)
          ->limit($limit)
          ->offset($offset);

        if (!$drafts) $query->condition('draft', 0);

        return $query->execute()
          ->fetchAll(\PDO::FETCH_CLASS, $this->subClass);
    }
    
    /**
     * Get the lastest (published) blog post
     * 
     * @param int $blogid
     * 
     * @return bool|\HamletCMS\BlogPosts\Post
     */
    public function getLatestPost($blogID)
    {
        $query = $this->getQuery()
          ->condition('blog_id', $blogID)
          ->condition('draft', 0)
          ->condition('timestamp', 'CURRENT_TIMESTAMP', '<')
          ->orderBy('timestamp', 'DESC');

        return $this->getPostFromQuery($query);
    }
    
    /**
     * Get the next (published) post in chronological order
     * 
     * @param int $blogID
     * @param string $currentPostTimestamp
     * 
     * @return bool|\HamletCMS\BlogPosts\Post
     */
    public function getNextPost($blogID, $currentPostTimestamp)
    {
        $query = $this->getQuery()
          ->condition('blog_id', $blogID)
          ->condition('draft', 0)
          ->condition('timestamp', $currentPostTimestamp, '>')
          ->condition('timestamp', 'CURRENT_TIMESTAMP', '<')
          ->orderBy('timestamp');

        return $this->getPostFromQuery($query);
    }
    
    /**
     * Get the previous (published) post in chronological order
     * 
     * @param int $blogID
     * @param string $currentPostTimestamp
     * 
     * @return bool|\HamletCMS\BlogPosts\Post
     */
    public function getPreviousPost($blogID, $currentPostTimestamp)
    {
        $query = $this->getQuery()
          ->condition('blog_id', $blogID)
          ->condition('draft', 0)
          ->condition('timestamp', $currentPostTimestamp, '<')
          ->condition('timestamp', 'CURRENT_TIMESTAMP', '<')
          ->orderBy('timestamp', 'DESC');
          
        return $this->getPostFromQuery($query);
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
        $query_string = "SELECT tp.class as classType, tp.* FROM {$this->tableName} as tp ";
        $query_string.= "WHERE blog_id='{$blogID}' ";
        $query_string.= "AND (title LIKE '%".Sanitize::string($searchterm)."%' OR tags LIKE '%".Sanitize::string($searchterm)."%') ";
        $query_string.= "AND draft=0 AND timestamp <= CURRENT_TIMESTAMP";

        $statement = $this->db->query($query_string);
        return $statement->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_CLASSTYPE);
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
     * @return bool|\HamletCMS\BlogPosts\Post[]
     */
    public function getPostCountByUser($blogID)
    {
        $query_string = "SELECT a.class as classType, a.author_id, count(a.*) as post_count, 
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
        return $statement->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_CLASSTYPE);
    }
    
    /**
     * Get all the posts on a blog
     * @param int $blogID - ID number of the blog
     * @param boolean $drafts - include draft posts or just live ones
     * @param boolean $future - include posts in future
     * 
     * @deprecated getPostsByBlog is better?
     */
    public function getAllPostsOnBlog(int $blogID, $drafts=0, $future=0)
    {
        $sql = "SELECT p.class as classType, p.* 
            FROM {$this->tableName} as p
            WHERE p.blog_id = '{$blogID}' ";

        if ($drafts == 0) $sql.= "AND p.draft='0' ";
        if ($future == 0) $sql.= "AND p.timestamp<='".date('Y-m-d H:i:s')."' ";
        $sql.= "ORDER BY p.timestamp DESC ";

        $statement = $this->db->query($sql);
        return $statement->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_CLASSTYPE);
    }
    
    /**
     * Get posts by blog
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
        // Ensure we cannot have negative page number
        // This would cause an SQL error
        if ($page < 1) $page = 1;

        $start = ($page-1) * $num;
        $tp = TBL_POSTS; $tv = TBL_POST_VIEWS; $tu = TBL_USERS;
        $sql = "SELECT $tp.class as classType, $tp.*, wordcount($tp.content) as wordcount, (SELECT count(*) FROM $tv WHERE $tv.postid = $tp.id) as uniqueviews, (SELECT COALESCE(SUM(userviews),0) from $tv WHERE $tv.postid = $tp.id) as hits, (SELECT username FROM $tu WHERE id = $tp.author_id) as username ";
        $sql.= "FROM $tp ";
        $sql.= "WHERE $tp.blog_id = '".$blogID."' ";

        if($drafts == 0) $sql.= "AND $tp.draft='0' ";
        if($future == 0) $sql.= "AND $tp.timestamp<='".date('Y-m-d H:i:s')."' ";
        
        $fields = array_merge($this->fields, ['uniqueviews' => 'number', 'hits' => 'number']);
        
        $splitSort = explode(' ', $sort);
        if (count($splitSort) == 2) {
            if (array_key_exists($splitSort[0], $fields)) {
                if (strtoupper($splitSort[1]) != 'ASC') {
                    $splitSort[1] = 'DESC';
                }
            }
            else {
                $sort = ''; // Invalid sort
            }
        }
        else $sort = ''; // Invalid sort
        
        if ($sort == '') {
            $sql.= "ORDER BY $tp.timestamp DESC ";
        }
        else {
            $sql.= "ORDER BY $splitSort[0] $splitSort[1] ";
        }
        
        $sql.= "LIMIT $start,$num";
        $statement = $this->db->query($sql);
        return $statement->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_CLASSTYPE);
    }

    /**
     * Get all posts that are published and have a publish date <= NOW
     * 
     * @param string|int $blogID
     * @param int|false $limit
     * @param int $pageIndex
     * 
     * @return \HamletCMS\BlogPosts\Post[]
     */
    public function getVisiblePosts($blogID, $limit=false, $pageIndex=0) {
        $query = $this->getQuery()
            ->condition('blog_id', $blogID)
            ->condition('draft', 0)
            ->condition('timestamp', 'CURRENT_TIMESTAMP', '<=')
            ->orderBy('timestamp', 'DESC');
        if ($limit) {
            $query->limit($limit);
            $query->offset($pageIndex * $limit);
        }
        return $this->getPostsFromQuery($query);
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
        $query_string = 'SELECT '.$this->tableName.'.class as classType, '.$this->tableName.'.*, '.TBL_BLOGS.'.name as blog_name FROM '.$this->tableName.' LEFT JOIN '.TBL_BLOGS.' ON '.$this->tableName.'.blog_id = '.TBL_BLOGS.'.id WHERE '.$this->tableName.'.timestamp >= DATE_SUB(NOW(), INTERVAL '.$daysSincePostLimit.' DAY) AND timestamp<="'.date('Y-m-d H:i:s').'" AND '.$this->tableName.'.draft = 0 ';

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
        return $statement->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_CLASSTYPE);
    }
    
    /**
     * Create a new post
     * 
     * @param array $newValues
     * 
     * @return bool Was the post created successfully?
     */
    public function createPost($newValues)
    {
        // A post must have title and content
        if (!array_key_exists('title', $newValues) || !array_key_exists('content', $newValues)) {
            return false;
        }
        
        if (!array_key_exists('timestamp', $newValues)) $newValues['timestamp'] = date("Y-m-d H:i:s");
        if (!array_key_exists('link', $newValues)) $newValues['link'] = $this->createSafePostUrl($newValues['title']);

        if (array_key_exists('tags', $newValues) && mb_strlen($newValues['tags']) > 0) {
            $newValues['tags'] = $this->createSafeTagList($newValues['tags']);
        }
        
        $currentUser = HamletCMS::session()->currentUser;
        $newValues['author_id'] = $currentUser['id'];
        
        return $this->insert($newValues);
    }
    
    /**
     * Clone a post
     * 
     * @return int new post ID
     */
    public function clonePost($postID)
    {
        $now = new \DateTime();
        $tempTableName = 'clonepost_'. $now->format('u');

        // Simple SQL row copy
        $query = $this->db->query("CREATE TEMPORARY TABLE `{$tempTableName}` SELECT * FROM {$this->tableName} WHERE id = {$postID}");
        if (!$query) return false;
        $query= $this->db->query("ALTER TABLE `{$tempTableName}` MODIFY id INT"); // make id nullable
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
            'author_id' => HamletCMS::session()->currentUser['id']
        ]);

        return $newPostID;
    }

    /**
     * Update a blog post
     * 
     * @param int $postid
     * @param array $newValues
     * 
     * @return bool Update success flag
     */
    public function updatePost($postid, $newValues)
    {
        $postid = Sanitize::int($postid);

        if (!array_key_exists('link', $newValues)) {
            $newValues['link'] = $this->createSafePostUrl($newValues['title']);
        }
        
        if (array_key_exists('tags', $newValues)) {
            $newValues['tags'] = $this->createSafeTagList($newValues['tags']);
        }
        
        return $this->update(['id' => $postid], $newValues);
    }
    
    /**
     * Create Safe Tag List makes sure all tags in a string are 'valid'
     * i.e. don't exist more than once, don't contain funny characters
     * and deals with spaces.
     * 
     * @param string $tags
     *   Comma seperated tag list
     * 
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
     * @param string $text - string to use for URL
     *
     * @return string a safe URL to make the pages more SEO friendly
     */
    public static function createSafePostUrl($text)
    {
        // Remove anything that isn't alphanumeric, dash or a space
        $postlink = preg_replace("/[^A-Za-z0-9\- ]/", '', $text);
        $postlink = strtolower(str_replace(" ", "-", Sanitize::string($postlink)));
        $postlink = substr($postlink, 0, 150);
        return $postlink;
    }
    
    /**
     * Get counts for all tags on a blog
     * @param <int> $blogid - blog to get counts
     * @return <array> of tuples - array[tagname,count]
     */
    public function countAllTagsByBlog($blogid, $sortby='text')
    {
        $posts = $this->getAllPostsOnBlog($blogid);
        $res = array();
        
        foreach ($posts as $post) {
            $tags = explode(",", $post->tags);
            if (count($tags) === 0) continue;
            
            foreach ($tags as $tag) {
                $tag = trim($tag);
                
                if (strlen($tag) === 0) continue;

                // Check if tag already in the array
                $countadded = $this->searchForTag($res, $tag);

                if ($countadded === false) {
                    $res[] = [
                        'text' => str_replace('+', ' ', $tag),
                        'slug' => strtolower($tag),
                        'count' => 1
                    ];
                }
                else {
                    $res[$countadded]['count'] += 1;
                }
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
     */
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

                $tag = strtolower(str_replace("+", " ", $tag));
                
                // Add to array if not already there
                if (!in_array($tag, $allTags)) $allTags[] = $tag;
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
    protected function searchForTag($tags, $tag)
    {
        for ($i = 0; $i < count($tags); $i++) {
            if($tags[$i]['slug'] == strtolower($tag)) return $i;
        }
        return false;
    }
    
    /**
     * Get all the posts on a blog with specified tag (must be exact)
     * 
     * @todo Not have to get all posts every time!
     * 
     * @param int $blogid - ID number of the blog
     * @param string $ptag - Tag
     *
     * @return array
     */
    public function getBlogPostsByTag($blogid, $ptag, $limit=-1, $page=0)
    {
        $posts = $this->getAllPostsOnBlog($blogid);
        $res = [];

        $offset = ($page-1) * $limit;
        $skipped = 0;
        $total = 0;
        
        // Loop through all tags in all posts
        foreach ($posts as $post) {
            $tags = explode(",", $post->tags);
            $hasTag = false;

            if (count($tags) == 0) {
                continue;
            }
            
            foreach ($tags as $tag) {
                $tag = str_replace("+", " ", $tag);

                if (strtolower(trim($tag)) == strtolower(trim($ptag))) {
                    $hasTag = true;
                    break;
                }
            }

            if ($hasTag) {
                $total++;

                // Paginate
                if ($page && $skipped < $offset) {
                    $skipped++;
                    continue;
                }

                // Apply limit
                if ($limit > 0 && count($res) == $limit) {

                }
                else {
                    $res[] = $post;
                }
            }
        }
        return [
            'posts' => $res,
            'total' => $total,
        ];
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
        $now = date('Y-m-d H:i:s');
        $sql = "UPDATE ".TBL_POST_VIEWS." SET userviews=userviews+1,last_viewed='$now' WHERE postid='$postid' AND userip='$userip'";
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
            'userviews' => 1,
            'last_viewed' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Get posts with the most views between 2 dates
     * 
     * @param int $blogID
     * @param string $startDate
     * @param string $endDate
     * 
     * @return \HamletCMS\BlogPosts\Post[]
     */
    public function getTrendingPosts($blogID, $startDate=null, $endDate=null)
    {
        if (is_null($startDate)) {
            // Default to 1 week ago
            $date1 = new \DateTime();
            $date1->sub(new \DateInterval('P1W'));
            $startDate = $date1->format('Y-m-d H:i:s');
        }
        if (is_null($endDate)) {
            // Default to now
            $date2 = new \DateTime();
            $endDate = $date2->format('Y-m-d H:i:s');
        }

        $subquery = $this->queryBuilder->select($this->tableName)
            ->fields(['id'])
            ->condition('blog_id', $blogID);

        $statement = $this->queryBuilder->select($this->tblviews, 'pv')
            ->leftJoin($this->tableName, 'p', 'p.id = pv.postid')
            ->fields(['p.class', 'p.*', 'count(*) AS userviewcount'])
            ->condition('postid', $subquery, 'IN')
            ->condition('pv.last_viewed', $endDate, '<=')
            ->condition('pv.last_viewed', $startDate, '>')
            ->groupBy('pv.postid')
            ->orderBy('userviewcount', 'DESC')
            ->execute();

        return $statement->fetchAll(\PDO::FETCH_CLASS | \PDO::FETCH_CLASSTYPE);
    }

}
