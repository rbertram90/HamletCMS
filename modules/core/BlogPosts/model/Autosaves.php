<?php

namespace HamletCMS\BlogPosts\model;

use HamletCMS\HamletCMS;
use rbwebdesigns\core\model\RBFactory;
use rbwebdesigns\core\Sanitize;

class Autosaves extends RBFactory
{
    /** @var \HamletCMS\BlogPosts\model\Posts */
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
        $this->modelPosts = HamletCMS::model('posts');
        
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
     * Autosave functionality
     * 
     * @param int $postID
     * @param mixed[] $data
     */
    public function autosavePost($postID, $data)
    {
        // $postCheck = $this->count(['id' => $postID]);
        $newTags = Posts::createSafeTagList($data['tags']);
        $currentUser = HamletCMS::session()->currentUser;

        // if (isset($data['timestamp'])) {
        //     $data['timestamp'] = date('Y-m-d H:i:s', strtotime($data['timestamp']));
        // }

        if ($postID <= 0) {
            // Post is not saved into the main table - create it as a draft
            $this->modelPosts->insert(array_merge($data, [
                'tags'            => $newTags,
                'author_id'       => $currentUser['id'],
                'draft'           => 1,
                'initialautosave' => 1,
                'link'            => $data['link'] ?? Posts::createSafePostUrl($data['title']),
                'timestamp'       => date('Y-m-d H:i:s')
            ]));
            
            $postID = $this->db->getLastInsertID();
        }
        else {
            // Update the post if it's only ever been autosaved.
            $arrayPost = $this->modelPosts->get('initialautosave', ['id' => $postID], '', '', false);

            // These fields should not be changed at this point.
            unset($data['type'], $data['blog_id']);

            if ($arrayPost->initialautosave == 1) {
                $update = $this->modelPosts->update(['id' => $postID], array_merge($data, [
                    'link' => $data['link'] ?? Posts::createSafePostUrl($data['title']),
                    'tags' => $newTags,
                ]));
            }
        }

        // These fields are not part of autosave table
        unset($data['type'], $data['blog_id'], $data['timestamp']);
        
        // Check for existing save for this post
        $autosaveCheck = $this->db->countRows($this->tableName, ["post_id" => $postID]);
        
        if ($autosaveCheck == 1) {
            // Found - Update
            $update = $this->db->updateRow($this->tableName, ['post_id' => $postID], array_merge($data, [
                'tags'            => $newTags,
                'link'            => $data['link'] ?? Posts::createSafePostUrl($data['title']),
                'date_last_saved' => date('Y-m-d H:i:s')
            ]));
            return $update ? $postID : false;
        }
        else {
            // Not Found - Create
            $insert = $this->db->insertRow($this->tableName, array_merge($data, [
                'post_id'         => $postID,
                'tags'            => $newTags,
                'link'            => $data['link'] ?? Posts::createSafePostUrl($data['title']),
                'date_last_saved' => date('Y-m-d H:i:s')
            ]));
            return $insert ? $postID : false;
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
        return $this->get('*', ['post_id' => $postID], null, null, false);
    }

}
