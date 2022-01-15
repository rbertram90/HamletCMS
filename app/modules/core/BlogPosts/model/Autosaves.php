<?php

namespace HamletCMS\BlogPosts\model;

use HamletCMS\HamletCMS;
use rbwebdesigns\core\model\RBFactory;
use rbwebdesigns\core\Sanitize;

class Autosaves extends RBFactory
{
    protected $modelPosts;

    /** @var \rbwebdesigns\core\Database */
    protected $db;

    /** @var string Class alias for Hamlet model map */
    public static $alias = 'autosaves';

    /**
     * Autosaves factory constructor
     * 
     * @param \rbwebdesigns\core\model\ModelManager $modelManager
     */
    function __construct($modelManager)
    {
        // Access to the database class
        
        $this->db = $modelManager->getDatabaseConnection();
        $this->tableName = TBL_AUTOSAVES;
        $this->subClass = '\\HamletCMS\\BlogPosts\\Autosave';
        $this->modelPosts = HamletCMS::model('\HamletCMS\BlogPosts\model\Posts');
        
        // @todo can we determine these dynamically?
        $this->fields = [
            'post_id' => 'number',
            'content' => 'string',
            'summary' => 'string',
            'title'   => 'string',
            'tags'    => 'string',
            'date_last_saved' => 'timestamp'
        ];

        HamletCMS::runHook('modelSchema', ['model' => $this]);
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
        $currentUser = HamletCMS::session()->currentUser;

        if ($postID <= 0) {
            // Post is not saved into the main table - create it as a draft
            $this->modelPosts->insert([
                'content'         => $data['content'],
                'title'           => $data['title'],
                'tags'            => $newTags,
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
            $arrayPost = $this->modelPosts->get('initialautosave', ['id' => $postID], '', '', false);
        
            if ($arrayPost->initialautosave == 1) {
                // Update the post
                $update = $this->modelPosts->update(['id' => $postID], [
                    'content'         => $data['content'],
                    'title'           => $data['title'],
                    'link'            => Posts::createSafePostUrl($data['title']),
                    'tags'            => $newTags,
                ]);
            }
        }
        
        // Check for existing save for this post
        $autosaveCheck = $this->db->countRows($this->tableName, ["post_id" => $postID]);
        
        if ($autosaveCheck == 1) {
            // Found - Update
            $update = $this->db->updateRow($this->tableName, ['post_id' => $postID], [
                'content'         => $data['content'],
                'title'           => $data['title'],
                'tags'            => $newTags,
                'date_last_saved' => date('Y-m-d H:i:s')
            ]);
            if ($update === false) return false;
            else return $postID;
        }
        else {
            // Not Found - Create
            $insert = $this->db->insertRow($this->tableName, array(
                'post_id'         => $postID,
                'content'         => $data['content'],
                'title'           => $data['title'],
                'tags'            => $newTags,
                'date_last_saved' => date('Y-m-d H:i:s')
            ));
            if ($insert === false) return false;
            else return $postID;
        }
    }
    
    public function removeAutosave($postID)
    {
        $postID = Sanitize::int($postID);
        return $this->db->deleteRow($this->tableName, ['post_id' => $postID]);
    }
    
    public function autosaveExists($postID)
    {
        $postID = Sanitize::int($postID);
        $savecount = $this->db->countRows($this->tableName, ['post_id' => $postID]);
        if($savecount == 1) return true;
        else return false;
    }
    
    public function getAutosave($postID)
    {
        $postid = Sanitize::int($postID);
        return $this->db->selectSingleRow($this->tableName, '*', ['post_id' => $postID]);
    }

}