<?php
namespace HamletCMS\EventLogger;

use HamletCMS\HamletCMS;

class Module
{

    /**
     * @var HamletCMS\EventLogger\model\EventLogger
     */
    protected $eventLogModel;

    public function __construct()
    {
        $this->eventLogModel = HamletCMS::model('\HamletCMS\EventLogger\model\EventLogger');
    }

    public function onPostCreated($data)
    {
        $post = $data['post'];
        $currentUser = HamletCMS::session()->currentUser;
        $text = "created post <a href='/blogs/{$post->blog_id}/posts/{$post->link}'>{$post->title}</a>";

        $this->eventLogModel->log($currentUser['id'], $post->blog_id, model\EventLogger::EVENT_POST_CREATED, $text);
    }

    public function onPostUpdated($data)
    {
        $post = $data['post'];
        $currentUser = HamletCMS::session()->currentUser;
        $text = "updated post <a href='/blogs/{$post->blog_id}/posts/{$post->link}'>{$post->title}</a>";

        $this->eventLogModel->log($currentUser['id'], $post->blog_id, model\EventLogger::EVENT_POST_UPDATED, $text);
    }

    public function onPostSettingsUpdated($data)
    {

    }

    /**
     * onPostDeleted
     * onTemplateChanged
     * onStylesheetUpdated
     * onPostSettingsUpdated
     * onBlogSettingsUpdated
     * onHeaderSettingsUpdated
     * onFooterSettingsUpdated
     * onPageSettingsUpdated
     * onWidgetsUpdated
     */

     
    /**
     * Run database setup
     */
    public function install()
    {
        $dbc = HamletCMS::databaseConnection();

        $dbc->query("CREATE TABLE `eventlog` (
            `id` int(11) NOT NULL,
            `user_id` int(11) NOT NULL,
            `activity_id` int(11) NOT NULL,
            `blog_id` bigint(12) NOT NULL,
            `text` varchar(255) NOT NULL,
            `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        $dbc->query("ALTER TABLE `eventlog` ADD PRIMARY KEY (`id`);");
        $dbc->query("ALTER TABLE `eventlog` MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;");
    }
    
}