<?php

namespace rbwebdesigns\blogcms\BlogPosts\model;

use rbwebdesigns\blogcms\BlogCMS;
use rbwebdesigns\core\model\RBFactory;

class Autosaves extends RBFactory
{
    /** @var \rbwebdesigns\core\Database $db */
    protected $db;
    /** @var string $tableName */
    protected $tableName;
    /** @var string $subClass */
    protected $subClass;

    /**
     * Autosaves factory constructor
     * 
     * @param \rbwebdesigns\core\ModelManager $modelManager
     */
    function __construct($modelManager)
    {
        // Access to the database class
        $this->db = $modelManager->getDatabaseConnection();
        $this->tblautosave = TBL_AUTOSAVES;
        $this->subClass = '\\rbwebdesigns\\blogcms\\BlogPosts\\Autosave';
        
        $this->fields = [
            'post_id' => 'number',
            'content' => 'string',
            'summary' => 'string',
            'title'   => 'string',
            'tags'    => 'string',
            'allowcomments' => 'boolean',
            'date_last_saved' => 'timestamp'
        ];
    }

    /**
     * Auto Save Functionality
     * 
     * @todo Add hook for custom post type fields!
     */
    public function autosavePost($postID, $data)
    {
        // $postCheck = $this->count(['id' => $postID]);
        $newTags = Posts::createSafeTagList($data['tags']);
        $currentUser = BlogCMS::session()->currentUser;

        if($postID <= 0) {
            // Post is not saved into the main table - create it as a draft
            $this->insert([
                'content'         => $data['content'],
                'title'           => $data['title'],
                'tags'            => $newTags,
                'allowcomments'   => $data['allowcomments'],
                'type'            => $data['type'],
                'blog_id'         => $data['blogID'],
                'author_id'       => $currentUser['id'],
                'draft'           => 1,
                'initialautosave' => 1,
                'link'            => Posts::createSafePostUrl($data['title']),
                'timestamp'       => date('Y-m-d H:i:s')
            ]);
                        
            $postID = $this->db->getLastInsertID();
        }
        else {
            $arrayPost = $this->get('initialautosave', ['id' => $postID], '', '', false);
        
            if($arrayPost['initialautosave'] == 1) {
                // Update the post
                $update = $this->update(['id' => $postID], [
                    'content'         => $data['content'],
                    'title'           => $data['title'],
                    'link'            => Posts::createSafePostUrl($data['title']),
                    'tags'            => $newTags,
                    'allowcomments'   => $data['allowcomments']
                ]);
            }
        }
        
        // Check for existing save for this post
        $autosaveCheck = $this->db->countRows($this->tblautosave, ["post_id" => $postID]);
        
        if($autosaveCheck == 1) {
            // Found - Update
            $update = $this->db->updateRow($this->tblautosave, ['post_id' => $postID], [
                'content'         => $data['content'],
                'title'           => $data['title'],
                'tags'            => $newTags,
                'allowcomments'   => $data['allowcomments'],
                'date_last_saved' => date('Y-m-d H:i:s')
            ]);
            if($update === false) return $false;
            else return $postID;
        }
        else {
            // Not Found - Create
            $insert = $this->db->insertRow($this->tblautosave, array(
                'post_id'         => $postID,
                'content'         => $data['content'],
                'title'           => $data['title'],
                'tags'            => $newTags,
                'allowcomments'   => $data['allowcomments'],
                'date_last_saved' => date('Y-m-d H:i:s')
            ));
            if($insert === false) return $false;
            else return $postID;
        }
    }
    
    public function removeAutosave($postID)
    {
        $postID = Sanitize::int($postID);
        return $this->db->deleteRow($this->tblautosave, ['post_id' => $postID]);
    }
    
    public function autosaveExists($postID)
    {
        $postID = Sanitize::int($postID);
        $savecount = $this->db->countRows($this->tblautosave, ['post_id' => $postID]);
        if($savecount == 1) return true;
        else return false;
    }
    
    public function getAutosave($postID)
    {
        $postid = Sanitize::int($postID);
        return $this->db->selectSingleRow($this->tblautosave, '*', ['post_id' => $postID]);
    }

}