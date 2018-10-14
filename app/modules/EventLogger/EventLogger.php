<?php
namespace rbwebdesigns\blogcms;

class EventLogger
{

    /**
     * @var rbwebdesigns\blogcms\EventLogger\model\EventLogger
     */
    protected $eventLogModel;

    public function __construct()
    {
        $this->eventLogModel = BlogCMS::model('\rbwebdesigns\blogcms\EventLogger\model\EventLogger');
    }

    public function onPostCreated($data)
    {
        $post = $data['post'];
        $currentUser = BlogCMS::session()->currentUser;
        $text = "created post <a href='/blogs/{$post['blog_id']}/posts/{$post['link']}'>{$post['title']}</a>";

        $this->eventLogModel->log($currentUser['id'], $post['blog_id'], EventLogger\model\EventLogger::EVENT_POST_CREATED, $text);
    }

    public function onPostUpdated($data)
    {
        $post = $data['post'];
        $currentUser = BlogCMS::session()->currentUser;
        $text = "updated post <a href='/blogs/{$post['blog_id']}/posts/{$post['link']}'>{$post['title']}</a>";

        $this->eventLogModel->log($currentUser['id'], $post['blog_id'], EventLogger\model\EventLogger::EVENT_POST_UPDATED, $text);
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
    
}