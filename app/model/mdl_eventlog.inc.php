<?php
namespace rbwebdesigns\blogcms\model;

use rbwebdesigns\core\model\RBFactory;

class EventLog extends RBFactory
{
    protected $tableName = 'eventlog';

    const EVENT_POST_CREATED = 0;
    const EVENT_POST_UPDATED = 1;
    const EVENT_POST_DELETED = 2;
    const EVENT_CONTRIBUTOR_ADDED = 3;
    const EVENT_CONTRIBUTOR_REMOVED = 4;
    const EVENT_CONTRIBUTOR_GROUP_CREATED = 5;
    const EVENT_CONTRIBUTOR_GROUP_UPDATED = 6;

    protected $fields = [
        'id'          => 'number',
        'user_id'     => 'number',
        'activity_id' => 'number',
        'blog_id'     => 'number',
        'text'        => 'string',
        'timestamp'   => 'timestamp',
    ];

    /**
     * Get recent activity log entries relating to blog
     */
    public function byBlog($blogID, $entries = 20)
    {
        return $this->db->query("SELECT eventlog.*, CONCAT(users.name, ' ', users.surname) as username FROM eventlog, users WHERE eventlog.blog_id = {$blogID} AND eventlog.user_id = users.id");
    }

    /**
     * Get recent activity log entries made by user
     */
    public function byUser($userID, $entries = 20)
    {
        return $this->db->query("SELECT eventlog.*, CONCAT(users.name, ' ', users.surname) as username FROM eventlog, users WHERE eventlog.user_id = {$userID} AND eventlog.user_id = users.id");
        // return $this->get('*', ['user_id' => $userID], 'id DESC', $entries);
    }

    /**
     * Insert a log entry
     */
    public function log($userID, $blogID, $activityID, $text)
    {
        return $this->insert([
            'blog_id' => $blogID,
            'user_id' => $userID,
            'activity_id' => $activityID,
            'text' => $text
        ]);
    }
}