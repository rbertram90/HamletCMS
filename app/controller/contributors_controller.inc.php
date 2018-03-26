<?php
namespace rbwebdesigns\blogcms;

use rbwebdesigns\core\Sanitize;

/**
 * /app/controller/contributors_controller.inc.php
 * 
 * @author R Bertram <ricky@rbwebdesigns.co.uk>
 */
class ContributorsController extends GenericController
{
    // Models
    protected $modelUsers;
    protected $modelBlogs;
    protected $modelPosts;
    protected $model;
    protected $modelGroups;
    protected $request, $response;
    
    public function __construct()
    {
        $this->modelUsers =  BlogCMS::model('\rbwebdesigns\blogcms\model\AccountFactory');
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\model\Blogs');
        $this->modelPosts = BlogCMS::model('\rbwebdesigns\blogcms\model\Posts');
        $this->model = BlogCMS::model('\rbwebdesigns\blogcms\model\Contributors');
        $this->modelGroups = BlogCMS::model('\rbwebdesigns\blogcms\model\ContributorGroups');
        
        $this->request = BlogCMS::request();
        $this->response = BlogCMS::response();

        BlogCMS::$activeMenuLink = 'users';
    }
    
    /**
     * Handles /contributors/manage/<blogid>
     */
    public function manage()
    {
        $blogID = $this->request->getUrlParameter(1);
        $blog = $this->modelBlogs->getBlogById($blogID);

        $currentUser = BlogCMS::session()->currentUser;

        if (!$this->model->isBlogContributor($blog['id'], $currentUser['id'], 'all')) {
            $this->response->redirect('/', 'Access denied', '');
        }

        $groups = $this->modelGroups->get('*', ['blog_id' => $blog['id']]);

        if (count($groups) == 0) {
            $this->modelGroups->createDefaultGroups($blog['id']);
            $groups = $this->modelGroups->get('*', ['blog_id' => $blog['id']]);
        }

        $this->response->setVar('groups', $groups);
        $this->response->setVar('contributors', $this->model->getBlogContributors($blog['id']));
        $this->response->setVar('postcounts', $this->modelPosts->countPostsByUser($blog['id'])); // Get the number of post each contributor has made
        $this->response->setVar('blog', $blog);
        $this->response->setTitle('Manage Blog Contributors - '.$blog['name']);
        $this->response->write('contributors/manage.tpl');
    }
    
    /**
     * Handles /contributors/create/<blogid>
     */
    public function create()
    {
        if ($this->request->method() == 'POST') return $this->runCreate();

        $blog = BlogCMS::getActiveBlog();

        $this->response->setVar('blog', $blog);
        $this->response->setTitle('Create Contributor');
        $this->response->write('contributors/create.tpl');
    }

    /**
     * Handles POST /contributors/create/<blogid>
     */
    protected function runCreate()
    {
        $blog = BlogCMS::getActiveBlog();
        
        $accountData = [
            'firstname'       => $this->request->getString('fld_name'),
            'surname'         => $this->request->getString('fld_surname'),
            'gender'          => $this->request->getString('fld_gender'),
            'username'        => $this->request->getString('fld_username'),
            'password'        => $this->request->getString('fld_password'),
            'passwordConfirm' => $this->request->getString('fld_password_2'),
            'email'           => $this->request->getString('fld_email'),
            'emailConfirm'    => $this->request->getString('fld_email_2'),
        ];

        // Validate
        if ($accountData['email'] != $accountData['emailConfirm']
            || $accountData['password'] != $accountData['passwordConfirm']) {
            $response->redirect('/contributors/manage', 'Email or passwords did not match', 'error');
        }

        $checkUser = $this->modelUsers->get('id', ['username' => $accountData['username']], '', '', false);
        if($checkUser && $checkUser['id']) {
            $response->redirect('/contributors/manage', 'Username is already taken', 'error');
        }

        if ($this->modelUsers->register($accountData)) {
            // Get the user ID of the user just created
            $user = $this->modelUsers->get('id', ['email' => $accountData['email']], '', '', false);
        }
        else {
            $this->response->redirect('/contributors/create/' . $blog['id'], 'Error creating account', 'error');
        }

        if (!$this->model->addBlogContributor($user['id'], 'p', $blog['id'])) {
            $this->response->redirect('/contributors/create/' . $blog['id'], 'Error assigning contributor', 'error');
        }

        $this->response->redirect('/contributors/manage/' . $blog['id'], 'Contributor created', 'success');
    }
    
    /**
     * Handles /contributors/permissions/<blogid>/<userid>
     */
    public function permissions()
    {
        
    }
    
    /**
     * Handles /contributors/remove/<blogid>/<userid>
     */
    public function remove()
    {
        
    }

    //----------------------------------------------------------
    // Groups
    //----------------------------------------------------------

    /**
     * Handles GET /contributors/editgroup
     */
    public function editgroup()
    {
        $groupID = $this->request->getUrlParameter(1);
        if (!$group = $this->modelGroups->getGroupById($groupID)) {
            $this->response->redirect('/', 'Group not found', 'error');
        }

        $blog = $this->modelBlogs->getBlogById($group['blog_id']);

        BlogCMS::$blogID = $blog['id'];

        $this->response->setVar('blog', $blog);
        $this->response->setVar('group', $group);
        $this->response->setTitle('Edit Group');
        $this->response->write('contributors/editgroup.tpl');
    }
    
}
