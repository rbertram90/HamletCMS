<?php

namespace HamletCMS\PostComments;

use HamletCMS\Blog\Blog;
use HamletCMS\BlogPosts\Post;
use HamletCMS\HamletCMS;
use HamletCMS\MenuLink;
use HamletCMS\HamletCMSResponse;

/**
 * class PostComments
 * 
 * @author R Bertram <ricky@rbwebdesigns.co.uk>
 */
class Module
{

    /**
     * Adds comments block to the user dashboard
     */
    public function content($args)
    {
        if ($args['key'] == 'userProfile') {
            $tempResponse = new HamletCMSResponse();
            $tempResponse->setVar('comments', HamletCMS::model('comments')->getCommentsByUser($args['user']->id, 0));
            $tempResponse->setVar('user', HamletCMS::model('useraccounts')->getById($args['user']->id));
            $args['content'] .= $tempResponse->write('recentcommentsbyuser.tpl', 'PostComments', false);
        }
    }

    /**
     * Run database setup
     */
    public function install()
    {
        $dbc = HamletCMS::databaseConnection();

        $dbc->query("CREATE TABLE `comments` (
            `id` int(8) NOT NULL,
            `message` text NOT NULL,
            `blog_id` bigint(10) NOT NULL,
            `post_id` int(8) NOT NULL,
            `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            `user_id` int(8) NOT NULL,
            `approved` int(11) NOT NULL DEFAULT '0'
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        $dbc->query("ALTER TABLE `comments` ADD PRIMARY KEY (`id`);");
        $dbc->query("ALTER TABLE `comments` MODIFY `id` int(8) NOT NULL AUTO_INCREMENT;");

        $dbc->query("ALTER TABLE `posts` ADD `allowcomments` tinyint(1);");
        $dbc->query("ALTER TABLE `postautosaves` ADD `allowcomments` tinyint(1);");
    }

    /**
     * Removes all traces of comments from the database
     */
    public function uninstall()
    {
        // delete database
        $dbc = HamletCMS::databaseConnection();
        $dbc->query("DROP TABLE IF EXISTS `comments`;");

        $dbc->query("ALTER TABLE `posts` DROP COLUMN `allowcomments`;");
        $dbc->query("ALTER TABLE `postautosaves` DROP COLUMN `allowcomments`;");
    }

    /**
     * 
     */
    public function modelSchema($args) {
        $targetClasses = [
            'HamletCMS\BlogPosts\model\Autosaves',
            'HamletCMS\BlogPosts\model\Posts'
        ];

        if (in_array(get_class($args['model']), $targetClasses) !== FALSE) {
            $args['model']->registerField('allowcomments', 'boolean');
        }
    }

    /**
     * Adds a total comment count to the blog dashboard
     */
    public function dashboardCounts($args)
    {
        if (!$args['blog']->commentsEnabled()) {
            return;
        }

        $args['counts']['Comments'] = HamletCMS::model('comments')->getCount(['blog_id' => $args['blog']->id]);
    }

    /**
     * Adds comments block to the blog dashboard
     */
    public function dashboardPanels($args)
    {
        if (!$args['blog']->commentsEnabled()) {
            return;
        }

        $tempResponse = new HamletCMSResponse();
        $tempResponse->setVar('blog', $args['blog']);
        $tempResponse->setVar('currentUser', HamletCMS::session()->currentUser);
        $tempResponse->setVar('comments', HamletCMS::model('comments')->getCommentsByBlog($args['blog']->id, 5));
        $args['panels'][] = $tempResponse->write('recentcommentsbyblog.tpl', 'PostComments', false);
    }

    /**
     * Adds comments section into bottom of single post view
     */
    public function runTemplate($args)
    {
        if ($args['template'] == 'singlePost' && $args['post']->allowComments()) {
            $args['post']->after[] = 'file:[PostComments]postcomments.tpl';
            $args['post']->after[] = 'file:[PostComments]newcommentform.tpl';

            /** @var \HamletCMS\BlogPosts\Post $post */
            $post = $args['post'];
            $blog = $post->blog();
            $customTemplateFile  = SERVER_PATH_BLOGS .'/'. $blog->id .'/templates/comment.tpl';
            $defaultTemplateFile = SERVER_MODULES_PATH .'/PostComments/templates/defaultcomment.tpl';
            $templatePath = file_exists($customTemplateFile) ? $customTemplateFile : $defaultTemplateFile;

            $args['response']->setVar('commentTemplatePath', $templatePath);
            $args['response']->setVar('comments', HamletCMS::model('comments')->getCommentsByPost($args['post']->id, false));
        }
    }

    public function editPostForm($args) {
        $active = is_null($args['post']) ? $args['blog']->commentsEnabled() : $args['post']->commentsEnabled();
        if ($active) {
            $args['fields'][] = 'file:[PostComments]allow-comments.tpl';
        }
    }

    /**
     * Add menu link into dropdown on index page
     */
    public function onGenerateMenu($args)
    {
        if ($args['id'] == 'bloglist') {
            $link = new MenuLink();
            $link->url = HamletCMS::route('comments.manage', [
                'BLOG_ID' => $args['blog']->id
            ]);
            $link->text = 'Comments';
            if ($link->url) {
                $args['menu']->addLink($link);
            }
        }
    }

    /**
     * Save allowcomments flag into posts table when post created/updated
     */
    public function onBeforePostSaved($args)
    {
        $request = HamletCMS::request();
        $args['post']['allowcomments'] = $request->getInt('comments');
    }

    /**
     * Save allowcomments flag into posts table when autosave created/updated
     */
    public function onBeforeAutosave(&$args) {
        $request = HamletCMS::request();
        $args['post']['allowcomments'] = $request->getInt('comments');
    }

    /**
     * Delete associated comments with a post
     */
    public function onPostDeleted($args) {
        $post = $args['post'];
        HamletCMS::model('comments')->delete(['post_id' => $post->id]);
    }

    /**
     * Delete associated comments with a blog
     */
    public function onDeleteBlog($args) {
        $blog = $args['blog'];
        HamletCMS::model('comments')->delete(['blog_id' => $blog->id]);
    }

    /**
     * Extend the Post (model) class
     */
    public function onPostConstruct($args)
    {
        $args['functions']['getComments'] = function(Post $post) {
            return HamletCMS::model('comments')->getCommentsByPost($post->id, false);
        };
        $args['functions']['commentsEnabled'] = function(Post $post) {
            return $post->blog()->commentsEnabled() && $post->allowComments;
        };
    }

    public function onBlogConstruct($args)
    {
        $args['functions']['commentsEnabled'] = function(Blog $blog) {
            return $blog->config()['comments']['enabled'] ?? 1;
        };
    }

    /**
     * Run tests
     */
    public function runUnitTests($args) {
        if ($args['context'] === 'post') {
            $test = new tests\CommentsTest();
            $test->postID = $args['post'];
            $test->blogID = $args['blogID'];
            $test->run();
        }
    }

    /**
     * Add comments field to post preview
     */
    public function onPreviewPost($args) {
        $post = $args['post'];
        $post->allowcomments = 0;
    }

}
