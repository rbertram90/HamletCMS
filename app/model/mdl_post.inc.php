<?php
namespace rbwebdesigns\blogcms;

use rbwebdesigns\core\model\RBFactory;
use rbwebdesigns\core\Sanitize;

/**
 * /app/model/mdl_post.inc.php
 * (All) Access to the posts database table is done through this class
 */
class ClsPost extends RBFactory
{
    protected $db, $dbc, $tableName;

    /**
     * Constructor
     * 
     * @param db $dbconn
     *   instance of database class (defined in core includes)
     */
    function __construct($dbconn)
    {
        // Access to the database class
        $this->db = $dbconn;
        // Connect to the database
        $this->dbc = $this->db->getConnection();
        
        // Set table names
        $this->tableName = TBL_POSTS;
        $this->tblviews = TBL_POST_VIEWS;
        $this->tblcontributors = TBL_CONTRIBUTORS;
        $this->tblautosave = TBL_AUTOSAVES;
        
        $this->fields = array(
            'id' => 'number',
            'title' => 'string',
            'content' => 'memo',
            'blog_id' => 'number',
            'link' => 'string',
            'draft' => 'boolean',
            'timestamp' => 'datetime',
            'allowcomments' => 'boolean',
            'tags' => 'string',
            'author_id' => 'number',
            'type' => 'string',
            'videoid' => 'string',
            'videosource' => 'string',
            'initialautosave' => 'boolean',
            'gallery_imagelist' => 'memo'
        );
    }
    
    /**
     *  Get all avaliable information on a single blog post
     *  @param <int> $postid - ID number of the post
    **/
    public function getPostById($postid) {
        return $this->db->selectSingleRow($this->tableName, '*', array('id' => $postid));
    }
    
    /**
     *  Get information on a post by sepcifying the url and blog id
     *   @param <string> Post title part of the URL
     *  @blogID <int> BlogID Number to uniquely identify the post
    **/
    public function getPostByURL($lsLink, $blogID) {
        return $this->db->selectSingleRow($this->tableName, '*', array(
            'link' => $lsLink,
            'blog_id' => $blogID
        ));
    }
    
    /**
     * Get the last blog post made on this blog
     * @param <int> $blogid - Blog ID number of the post
    **/
    public function getLatestPost($blogid) {
        $arrayWhere = array('blog_id' => $blogid, 'timestamp' => '< CURRENT_TIMESTAMP', 'draft' => 0);
        return $this->db->selectSingleRow($this->tableName, '*', $arrayWhere, 'timestamp DESC', '1');
    }
    
    public function getNextPost($blogid, $currentPostTimestamp) {
        $arrayWhere = array('timestamp' => '>'.$currentPostTimestamp, 'blog_id' => $blogid, 'draft' => 0);
        $result = $this->db->selectSingleRow($this->tableName, '*', $arrayWhere, 'timestamp ASC', '1');
        
        // Only Return Result if the post is not scheduled
        // Note this could be done using SQL however the database class cannot handle duplicate keys...
        if($result['timestamp'] < date('Y-m-d H:i:s')) return $result;
    }
    
    public function getPreviousPost($blogid, $currentPostTimestamp) {
        $arrayWhere = array('blog_id' => $blogid, 'timestamp' => '<'.$currentPostTimestamp, 'draft' => 0);
        return $this->db->selectSingleRow($this->tableName, '*', $arrayWhere, 'timestamp DESC', '1');
    }
    
    public function search($blogid, $searchterm) {
        
        // Search posts by title & tags
        $query_string = "SELECT * FROM {$this->tableName} ";
        $query_string.= "WHERE blog_id='{$blogid}' ";
        $query_string.= "AND (title LIKE '%".Sanitize::string($searchterm)."%' OR tags LIKE '%".Sanitize::string($searchterm)."%') ";
        $query_string.= "AND draft=0 AND timestamp <= CURRENT_TIMESTAMP";

        $statement = $this->db->query($query_string);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Count the number of posts on a blog
     * @param <int> $pBlogID - ID number of the blog
     * @param <bool> $incDrafts - Include draft posts in this count
    **/
    public function countPostsOnBlog($blogid, $incDrafts = false, $incfutures = false) {
        $query_string = 'SELECT count(*) as countvar FROM ' . $this->tableName . ' WHERE blog_id="'.$blogid.'"';
        if(!$incfutures) $query_string.= 'AND timestamp<="'.date('Y-m-d H:i:s').'"';
        if(!$incDrafts) $query_string.= " and draft=0";
        
        $statement = $this->db->query($query_string);
        return $statement->fetch(\PDO::FETCH_ASSOC)['countvar'];
    }
    
    // Get count of posts and the date of the last post for all contributors for a blog
    public function countPostsByUser($blogid)
    {
        $query_string = "SELECT author_id, count(*) as post_count, ";
        $query_string.= "   (";
        $query_string.= "    SELECT timestamp ";
        $query_string.= "    FROM {$this->tableName} as b ";
        $query_string.= "    WHERE blog_id='{$blogid}' ";
        $query_string.= "    AND author_id=a.author_id ";
        $query_string.= "    AND timestamp < CURRENT_TIMESTAMP ";
        $query_string.= "    ORDER BY timestamp DESC ";
        $query_string.= "    LIMIT 1";
        $query_string.= "   ) as last_post ";
        $query_string.= "FROM {$this->tableName} as a ";
        $query_string.= "WHERE blog_id='{$blogid}' ";
        $query_string.= "AND draft=0 ";
        $query_string.= "AND timestamp<='".date('Y-m-d H:i:s')."' ";
        $query_string.= "GROUP BY author_id ";
        $query_string.= "ORDER BY timestamp DESC";
        
        $statement = $this->db->query($query_string);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    public function countTotalPostViews($blogid)
    {
        $qs = "SELECT sum(userviews) as totalViews FROM ".$this->tblviews." WHERE postid in (SELECT id FROM ".$this->tableName." WHERE blog_id='$blogid')";
        $statement = $this->db->query($qs);
        $result = $statement->fetch(\PDO::FETCH_ASSOC);

        if(strlen($result['totalViews']) == 0) $result['totalViews'] = 0;
        return Sanitize::int($result['totalViews']);
    }
    
    /**
     * Get all the posts on a blog - deprecate?
     * <boolean> $drafts - include draft posts or just live ones
     * <int> $pBlogID - ID number of the blog
    **/
    public function getAllPostsOnBlog($pBlogID, $drafts=0, $future=0)
    {
        $tp = TBL_POSTS; $tc = TBL_COMMENTS;
        $sql = "SELECT $tp.*, (SELECT count(*) from $tc WHERE $tc.post_id = $tp.id) as numcomments ";
        $sql.= "FROM $tp ";
        $sql.= "WHERE $tp.blog_id = '".$pBlogID."' ";
        if($drafts == 0) $sql.= "AND $tp.draft='0' ";
        if($future == 0) $sql.= "AND $tp.timestamp<='".date('Y-m-d H:i:s')."'";
        $sql.= "ORDER BY $tp.timestamp DESC ";
        $statement = $this->db->query($sql);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    /**
     * Get <array> posts from $blog including number of comments for each post
     * @param <int> $num - number of posts to get
     * @param <int> $page - page number
     * @param <boolean> $drafts - include draft posts or just live ones
     * @param <int> $pBlogID - ID number of the blog
    **/
    public function getPostsByBlog($pBlogID, $page=1, $num=10, $drafts=0, $future=0, $sort='')
    {
        $start = ($page-1) * $num;
        $tp = TBL_POSTS; $tc = TBL_COMMENTS; $tv = TBL_POST_VIEWS; $tu = TBL_USERS;
        $sql = "SELECT $tp.*, wordcount($tp.content) as wordcount, (SELECT count(*) from $tc WHERE $tc.post_id = $tp.id) as numcomments, (SELECT count(*) FROM $tv WHERE $tv.postid = $tp.id) as uniqueviews, (SELECT COALESCE(SUM(userviews),0) from $tv WHERE $tv.postid = $tp.id) as hits, (SELECT username FROM $tu WHERE id = $tp.author_id) as username ";
        $sql.= "FROM $tp ";
        $sql.= "WHERE $tp.blog_id = '".$pBlogID."' ";
        if($drafts == 0) $sql.= "AND $tp.draft='0' ";
        if($future == 0) $sql.= "AND $tp.timestamp<='".date('Y-m-d H:i:s')."'";
        
        $fields = array_merge($this->fields, array('numcomments' => 'number', 'uniqueviews' => 'number', 'hits' => 'number'));
        
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
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
        
    /**
     * Get Recent Posts - Get posts made within the last X days for either a single or an array of blogs
     * @param <int> $pBlogID : id number of the blog to get posts for / array of blog id's
     * @param <date> $pDaysSincePostLimit : default 5 = get posts within the last 5 days
     * @return <array> list of posts that were made after @param timestamp
    **/
    public function getRecentPosts($pBlog, $pDaysSincePostLimit=7) {
        
        $query_string = 'SELECT '.$this->tableName.'.*, '.TBL_BLOGS.'.name as blog_name FROM '.$this->tableName.' LEFT JOIN '.TBL_BLOGS.' ON '.$this->tableName.'.blog_id = '.TBL_BLOGS.'.id WHERE '.$this->tableName.'.timestamp >= DATE_SUB(NOW(), INTERVAL '.$pDaysSincePostLimit.' DAY) AND timestamp<="'.date('Y-m-d H:i:s').'" AND '.$this->tableName.'.draft = 0';
        
        if(gettype($pBlog) == "array") {
            if(count($pBlog) == 0) return false;
            foreach($pBlog as $key => $blog) {                
                if($key == 0) $query_string.=  " AND (";
                else $query_string.= " OR ";
                $query_string.= $this->tableName.".blog_id='".$blog['blog_id']."'";
            }
            $query_string.= ")";
            
        } else if(gettype($pBlog) == "integer") {
            $query_string.= 'AND '.$this->tableName.'.blog_id="'.safeNumber($pBlog).'"';
            
        } else return false;
        
        $query_string.= " ORDER BY ".$this->tableName.".timestamp DESC LIMIT 30";
        $statement = $this->db->query($query_string);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    
    /**
     * Check if a user contributes to a blog
     * @param <int> $pUser - User ID for target user
     * @param <int> $pBlog - Blog ID for target blog
    **/
    public function isContributor($userID, $blogID) {
        $query = $this->db->countRows($this->tblcontributors, array(
            'user_id' => $userID,
            'blog_id' => $blogID
        ));
        return ($query > 0);
    }
    
    
    /**
     * Create a new post
     * @param <array> $values
    **/
    public function createPost($newValues) {
        
        $currentUser = BlogCMS::session()->currentUser;

        // Make sure that the current user has the permission to post to this blog
        if(!$this->isContributor($currentUser, $newValues['blog_id'])) {
            die("User not authorised to post to this blog!");
            return;
        }
        
        if(!array_key_exists('title', $newValues) || !array_key_exists('content', $newValues)) {
            die("Unable to continue - TITLE and CONTENT fields are mandatory");
        }
        
        // System / Defaulted values
        if(!array_key_exists('draft', $newValues)) $newValues['draft'] = 0;
        if(!array_key_exists('allowcomments', $newValues)) $newValues['allowcomments'] = 1;
        if(!array_key_exists('type', $newValues)) $newValues['type'] = 'standard';
        $newValues['timestamp'] = date("Y-m-d H:i:s");
        $newValues['link'] = $this->createSafePostUrl($newValues['title']);
        $newValues['tags'] = $this->createSafeTagList($newValues['tags']);
        $newValues['author_id'] = $currentUser;
        
        return $this->insert($newValues);
    }
    
    
    /**
     * Update a blog post
     * @param <string> $pTitle - Title of the post
     * @param <text> $pContent - Post content
     * @param <string> $pTags - CSV of tags
     * @param <number> $pPost - ID of the post to update
     * @param <boolean> $pDraft - Is the post a draft (not live on blog)
    **/
    public function updatePost($postid, $newValues) {
        
        $postid = Sanitize::int($postid);
        
        // if(!array_key_exists('link', $newValues))
        // automattically create link
        $newValues['link'] = $this->createSafePostUrl($newValues['title']);
        
        if(array_key_exists('tags', $newValues))
          $newValues['tags'] = $this->createSafeTagList($newValues['tags']);
        
        return $this->update(array('id' => $postid), $newValues);
    }
    
    
    /**
     * Create Safe Tag List makes sure all tags in a string are 'valid'
     * i.e. don't exist more than once, don't contain funny characters
     * and deals with spaces.
     * @param <string> $lsTagCSV - comma seperated tag list
     * @return <string> - CSV of formatted, valid tags
    **/
    private function createSafeTagList($lsTagCSV) {
    
        $splitCSV = explode(",", $lsTagCSV);
        $validTagsString = "";
        $validTagsArray = Array();
        
        foreach($splitCSV as $tag):
            // Remove Whitespace
            $validTag = trim($tag);
            // Remove anything that isn't alphanumeric or a space
            $validTag = preg_replace("/[^A-Za-z0-9 ]/", '', $validTag); 
            // Convert Spaces to +
            $validTag = str_replace(" ","+",$validTag);
            // Check the entry doesn't already exists
            if(array_search($validTag, $validTagsArray) === false):
                // Append to new CSV
                if(strlen($validTagsString) > 0) $validTagsString.= ",".$validTag;
                else $validTagsString.= $validTag;
                // Add to array also
                array_push($validTagsArray, $validTag);
            endif;
        endforeach;
        
        return $validTagsString;
    }
    
    
    /**
     * Create a safe URL for a post (no funny characters)
     * @param <string> $text - string to use for URL
     * @return <string> a safe URL to make the pages more SEO friendly
    **/
    private function createSafePostUrl($text) {
    
        // Remove anything that isn't alphanumeric or a space
        $postlink = preg_replace("/[^A-Za-z0-9 ]/", '', $text);
        // Replace Spaces with dashes
        $postlink = strtolower(str_replace(" ", "-", safeString($postlink)));
        
        return $postlink;
    }
    
    
    /**
     * Get counts for all tags on a blog
     * @param <int> $blogid - blog to get counts
     * @return <array> of tuples - array[tagname,count]
     */
    public function countAllTagsByBlog($blogid) {
    
        // Get the posts from this blog
        $lobjPosts = $this->getAllPostsOnBlog($blogid);
        $res = array();
        
        // Loop through the posts
        foreach($lobjPosts as $post):
            // Create array from CSV string
            $tags = explode(",", $post['tags']);
            // Check this post has tags
            if(count($tags) === 0) continue;
            // Loop through tags
            foreach($tags as $tag):
                $tag = trim($tag);
                // Check tag is not empty
                if(strlen($tag) === 0) continue;
                // Is this tag already part of the array?
                $countadded = $this->searchForTag($res, $tag);
                // Increment count depending on if it key already exists
                if($countadded === false) $res[] = array(strtolower($tag),1);
                else $res[$countadded][1] += 1;
            endforeach;
        endforeach;
        
        // Sort by name
        sksort($res, 0, true);
        
        return $res;
    }

    
    /**
     * Get all unique tags that have been applied
     * to posts for this blog
    **/
    public function getAllTagsByBlog($blogId) {
    
        // Get the posts from this blog
        $posts = $this->getAllPostsOnBlog($blogId);
        $allTags = array();
        
        // Loop through the posts
        foreach($posts as $post):
        
            // Create array from CSV string
            $tags = explode(",", $post['tags']);
            
            // Check this post has tags
            if(count($tags) === 0) continue;
        
            foreach($tags as $tag):
                
                $tag = trim($tag);
                
                // Check tag is not empty
                if(strlen($tag) === 0) continue;
                
                // Add to array if not already there
                if(!in_array($tag, $allTags)) $allTags[] = strtolower($tag);
                
            endforeach;
        endforeach;
        
        // Sort by name
        sort($allTags);
        
        return $allTags;
    }
    
    
    /**
     * Check if the tag is in an array
     * @param <string> $pTag - needle to find
     * @param <array> $pArray - array to search
     * @return <bool> if the tag exists returns position, false otherwise
    **/
    private function searchForTag($pArray, $pTag) {
        for($i = 0; $i < count($pArray); $i++):
            if($pArray[$i][0] == strtolower($pTag)) return $i;
        endfor;
        return false;
    }
    
    
    /**
     * Get all the posts on a blog with specified tag (must be exact)
     * @param <int> $blogid - ID number of the blog
     * @param <string> $ptag - Tag
     * @return <array> of posts
    **/
    public function getBlogPostsByTag($blogid, $ptag) {
        
        $lobjPosts = $this->getAllPostsOnBlog($blogid);
        $res = array();
        
        // Loop through all tags in all posts
        foreach($lobjPosts as $post):
        
            $tags = explode(",", $post['tags']);
            if(count($tags) == 0) continue;
            
            foreach($tags as $tag):
                $tag = str_replace("+"," ",$tag);
                // Compare - Case Insensitive
                if(strtolower(trim($tag)) == strtolower(trim($ptag))) $res[] = $post;
            endforeach;
        
        endforeach;
        return $res;
    }
    
    
    /**
     * Delete an existing post - DEPRECATED - User model->delete - Permissions checked in controller?!
     * @param <int> $blogid - id number of the blog to delete post from
     * @param <int> $postid - id number of the post to delete
     * NOTE - this function should also delete comments associated with this post!
    **/
    public function xxdeletePost($blogid, $postid) {
    
        // Sanitize Variables
        $blogid = safeNumber($blogid);
        $postid = safeNumber($postid);

        $currentUser = BlogCMS::session()->currentUser;
        
        // Make sure that the current user has the permission to post to this blog
        if(!$this->isContributor($currentUser, $blogid)) return false;
        
        // Query DB
        $sql = 'DELETE FROM '.$this->tableName.' WHERE id="'.$postid.'"';
        $this->db->runQuery($sql);
        
        // Remove the autosave if exists
        if($this->autosaveExists($postid))
        {
            $this->removeAutosave($postid);
        }
        
        return true;
    }
        
    
    /**
     * Get all IP that have viewed this post
     * @param $postid - id for the post that is viewed
    **/
    public function getViewsByPost($postid) {
        $postid = Sanitize::int($postid);
        $sql = 'SELECT * FROM '.TBL_POST_VIEWS.' WHERE postid = "'.$postid.'"';
        $statement = $this->db->query($sql);
        return $statement->fetchAll(\PDO::FETCH_ASSOC);
    }
    
    
    /**
     * Increment a specific users' view count (by IP)
     * @param $postid - id for the post that is viewed
     * @param $userip - the user's ip
     * @param $updatedviewcount - not used!?
    **/
    public function incrementUserView($postid, $userip, $updatedviewcount) {        
        $sql = "UPDATE ".TBL_POST_VIEWS." SET userviews=userviews+1 WHERE postid='$postid' AND userip='$userip'";
        $this->db->runQuery($sql);
    }
    
    
    /**
     * Add a new user row to the post view table
     * @param $postid - id for the post that is viewed
     * @param $userip - the user's ip
    **/
    public function recordUserView($postid, $userip) {
        $this->db->insertRow(
            TBL_POST_VIEWS,
            array(
                'postid'=> $postid,
                'userip' => $userip,
                'userviews' => 1
            )
        );
    }
    
    /**
     * Auto Save Functionality
    **/
    public function autosavePost() {
    
        $postid = Sanitize::int($_POST['fld_postid']);
        $postCheck = $this->db->countRows($this->tableName, array("id" => $postid));
        $postInserted = false;
        
        $newContent = Sanitize::string($_POST['fld_content']);
        $newTitle = Sanitize::string($_POST['fld_title']);
        $newTags = $this->createSafeTagList($_POST['fld_tags']);
        $newCommentFlag = Sanitize::int($_POST['fld_allowcomments']);
        $type = Sanitize::string($_POST['fld_type']);
        
        $currentUser = BlogCMS::session()->currentUser;

        if($postid <= 0 || $postCheck == 0) {
            // Post is not saved into the main table - create it as a draft
            $this->db->insertRow($this->tableName, array(
                'content'         => $newContent,
                'title'           => $newTitle,
                'tags'            => $newTags,
                'allowcomments'   => $newCommentFlag,
                'draft'           => 1,
                'type'            => $type,
                'blog_id'         => Sanitize::int($_POST['fld_blogid']),
                'author_id'       => Sanitize::int($currentUser),
                'initialautosave' => 1,
                'link'            => $this->createSafePostUrl($newTitle),
                'timestamp'       => date('Y-m-d H:i:s')
            ));
            
            $postInserted = true;
            
            // Record the new post id
            $postid = $this->db->getLastInsertID();
        }
        
        $arrayPost = $this->db->selectSingleRow($this->tableName, 'initialautosave', array('id' => $postid));
        
        if($arrayPost['initialautosave'] == 1 && !$postInserted) {
            // Update the post
            $update = $this->db->updateRow($this->tableName, array('id' => $postid), array(
                'content'         => $newContent,
                'title'           => $newTitle,
                'link'            => $this->createSafePostUrl($newTitle),
                'tags'            => $newTags,
                'allowcomments'   => $newCommentFlag
            ));
        }
        
        // Check for existing save for this post
        $autosaveCheck = $this->db->countRows($this->tblautosave, array("post_id" => $postid));
        
        if($autosaveCheck == 1) {
            // Found - Update
            $update = $this->db->updateRow($this->tblautosave, array('post_id' => $postid), array(
                'content'         => $newContent,
                'title'           => $newTitle,
                'tags'            => $newTags,
                'allowcomments'   => $newCommentFlag,
                'date_last_saved' => date('Y-m-d H:i:s')
            ));
            if($update === false) return $false;
            else return $postid;
            
        } else {
            // Not Found - Create
            $insert = $this->db->insertRow($this->tblautosave, array(
                'post_id'         => $postid,
                'content'         => $newContent,
                'title'           => $newTitle,
                'tags'            => $newTags,
                'allowcomments'   => $newCommentFlag,
                'date_last_saved' => date('Y-m-d H:i:s')
            ));
            if($insert === false) return $false;
            else return $postid;
        }
    }
    
    public function removeAutosave($postid)
    {
        $postid = Sanitize::int($postid);
        $this->db->deleteRow($this->tblautosave, array('post_id' => $postid));
        // if($deletesuccess === false)
    }
    
    public function autosaveExists($postid)
    {
        $postid = Sanitize::int($postid);
        $savecount = $this->db->countRows($this->tblautosave, array('post_id' => $postid));
        if($savecount == 1) return true;
        else return false;
    }
    
    public function getAutosave($postid)
    {
        $postid = Sanitize::int($postid);
        return $this->db->selectSingleRow($this->tblautosave, '*', array('post_id' => $postid));
    }
}
?>