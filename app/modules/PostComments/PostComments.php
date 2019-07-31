<?php

namespace rbwebdesigns\blogcms;

/**
 * class PostComments
 * 
 * @author R Bertram <ricky@rbwebdesigns.co.uk>
 */
class PostComments
{
    protected $model;

    public function __construct()
    {
        $this->model = BlogCMS::model('\rbwebdesigns\blogcms\PostComments\model\Comments');
    }

    /**
     * Adds comments block to the user dashboard
     */
    public function content($args)
    {
        if ($args['key'] == 'userProfile') {
            $tempResponse = new BlogCMSResponse();
            $tempResponse->setVar('comments', $this->model->getCommentsByUser($args['user']->id, 0));
            $args['content'] .= $tempResponse->write('recentcommentsbyuser.tpl', 'PostComments', false);
        }
    }

    /**
     * Run database setup
     */
    public function install()
    {
        $dbc = BlogCMS::databaseConnection();

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
        $dbc = BlogCMS::databaseConnection();
        $dbc->query("DROP TABLE IF EXISTS `comments`;");

        $dbc->query("ALTER TABLE `posts` DROP COLUMN `allowcomments`;");
        $dbc->query("ALTER TABLE `postautosaves` DROP COLUMN `allowcomments`;");
    }

    /**
     * 
     */
    public function modelSchema($args) {
        $targetClasses = [
            'rbwebdesigns\blogcms\BlogPosts\model\Autosaves',
            'rbwebdesigns\blogcms\BlogPosts\model\Posts'
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
        $args['counts']['comments'] = $this->model->getCount(['blog_id' => $args['blog']->id]);
    }

    /**
     * Adds comments block to the blog dashboard
     */
    public function dashboardPanels($args)
    {
        $tempResponse = new BlogCMSResponse();
        $tempResponse->setVar('blog', $args['blog']);
        $tempResponse->setVar('currentUser', BlogCMS::session()->currentUser);
        $tempResponse->setVar('comments', $this->model->getCommentsByBlog($args['blog']->id, 5));
        $args['panels'][] = $tempResponse->write('recentcommentsbyblog.tpl', 'PostComments', false);
    }

    /**
     * Adds comments section into bottom of single post view
     */
    public function runTemplate($args)
    {
        if ($args['template'] == 'singlePost' && $args['post']->allowcomments) {
            $args['post']->after[] = 'file:[PostComments]postcomments.tpl';
            $args['post']->after[] = 'file:[PostComments]newcommentform.tpl';

            $args['response']->setVar('comments', $this->model->getCommentsByPost($args['post']->id, false));
        }
    }

    public function editPostForm($args) {
        $args['fields'][] = 'file:[PostComments]allow-comments.tpl';
    }

    /**
     * Add menu link into dropdown on index page
     */
    public function onGenerateMenu($args)
    {
        if ($args['id'] == 'bloglist') {

            $link = new MenuLink();
            $link->url = BlogCMS::route('comments.all', [
                'BLOG_ID' => $args['blog']->id
            ]);
            $link->text = 'Comments';
            $args['menu']->addLink($link);
        }
    }

    /**
     * Save allowcomments flag into posts table when post created/updated
     */
    public function onBeforePostSaved($args)
    {
        $request = BlogCMS::request();
        $args['post']['allowcomments'] = $request->getInt('comments');
    }

    public function onBeforeAutosave(&$args) {
        $request = BlogCMS::request();
        $args['post']['allowcomments'] = $request->getInt('comments');
    }

    public function onDeleteBlog($args) {
        $blog = $args['blog'];
        $this->model->delete(['blog_id' => $blog->id]);
    }

    public function onPostConstruct($args) {
        // $args['post']->allowcomments = ;

        $args['functions']['getComments'] = function($post) {
            return $this->model->getCommentsByPost($post->id, false);
        };
        $args['functions']['commentsEnabled'] = function($post) {
            return $post->allowComments;
        };
        
    }

}
