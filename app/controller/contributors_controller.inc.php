<?php
namespace rbwebdesigns\blogcms;

use rbwebdesigns\blogcms\model\ContributorGroups;
use rbwebdesigns\core\Sanitize;
use rbwebdesigns\core\JSONHelper;

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
    protected $blog;
    
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

        $this->setup();
    }

    /**
     * Setup common objects
     * Checks the user has permissions to run the request
     */
    protected function setup()
    {
        if (!$this->blog = BlogCMS::getActiveBlog()) {
            // Danger - need to handle permissions seperately!
            return;
        }

        $this->checkUserAccess();
    }

    /**
     * Check the user has access to view/change the contributors of the blog
     * Requires $this->blog to be set, redirects if not got permission
     */
    protected function checkUserAccess()
    {
        $currentUser = BlogCMS::session()->currentUser;
        $access = true;

        if (!$this->blog) $access = false;

        elseif (!$this->model->userHasPermission($currentUser['id'], $this->blog['id'], ContributorGroups::GROUP_MANAGE_CONTRIBUTORS)) {
            $access = false;
        }

        if (!$access) {
            $this->response->redirect('/', '403 Access Denied', 'error');
        }
    }
    
    /**
     * Handles /contributors/manage/<blogid>
     */
    public function manage()
    {
        $groups = $this->modelGroups->get('*', ['blog_id' => $this->blog['id']]);

        if (count($groups) == 0) {
            $this->modelGroups->createDefaultGroups($this->blog['id']);
            $groups = $this->modelGroups->get('*', ['blog_id' => $this->blog['id']]);
        }

        $this->response->setVar('groups', $groups);
        $this->response->setVar('contributors', $this->model->getBlogContributors($this->blog['id']));
        $this->response->setVar('postcounts', $this->modelPosts->countPostsByUser($this->blog['id'])); // Get the number of post each contributor has made
        $this->response->setVar('blog', $this->blog);
        $this->response->setTitle('Manage Blog Contributors - '.$this->blog['name']);
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
     * Handles /contributors/edit/<blogid>/<userid>
     */
    public function edit()
    {
        $contributorID = $this->request->getUrlParameter(2);

        if (!$user = $this->modelUsers->getById($contributorID)) {
            $this->response->redirect('/', 'Unable to find contributor', 'error');
        }

        if ($this->request->method() == 'POST') {
            return $this->runUpdateContributor($user);
        }

        $this->response->setVar('contributor', $user);
        $this->response->setVar('blog', $this->blog);
        $this->response->setVar('groups', $this->modelGroups->get('*', ['blog_id' => $this->blog['id']]));
        $this->response->write('contributors/edit.tpl');
    }
    
    /**
     * Handles /contributors/remove/<blogid>/<userid>
     * Confirmation happened client side
     */
    public function remove()
    {
        $contributorID = $this->request->getUrlParameter(2);

        if (!$user = $this->modelUsers->getById($contributorID)) {
            $this->response->redirect('/', 'Unable to find contributor', 'error');
        }

        if ($this->model->delete(['blog_id' => $this->blog['id'], 'user_id' => $contributorID])) {
            $this->response->redirect('/contributors/manage/' . $this->blog['id'], 'Contributor removed', 'success');
        }
        else {
            $this->response->redirect('/contributors/manage/' . $this->blog['id'], 'Unable to remove contributor', 'error');
        }
    }

    //----------------------------------------------------------
    // Groups
    //----------------------------------------------------------

    /**
     * Handles GET /contributrs/creategroup/<blogid>
     */
    public function creategroup()
    {
        if ($this->request->method() == 'POST') {
            return $this->runCreateGroup();
        }

        $this->response->setVar('blog', $this->blog);
        $this->response->setTitle('Add contributors group');
        $this->response->write('contributors/creategroup.tpl');
    }

    /**
     * Handles GET /contributrs/creategroup/<blogid>
     */
    protected function runCreateGroup()
    {
        $permissions = $this->request->get('fld_permission');

        if (gettype($permissions) != 'array') {
            $this->response->redirect('/', 'Data error', 'error');
        }

        $systemPermissions = [
            "create_posts","publish_posts","edit_all_posts","delete_posts",
            "manage_comments","delete_files","change_settings","manage_contributors"
        ];

        $data = [];

        foreach ($systemPermissions as $permission) {
            if (array_key_exists($permission, $permissions) && $permissions[$permission] == 'on') {
                $data[$permission] = 1;
            }
            else {
                $data[$permission] = 0;
            }
        }

        $insert = $this->modelGroups->insert([
            'blog_id'     => $this->blog['id'],
            'name'        => $this->request->getString('fld_name'),
            'description' => $this->request->getString('fld_description'),
            'data'        => JSONHelper::arrayToJSON($data),
            'locked'      => 0
        ]);

        if ($insert) {
            $this->response->redirect('/contributors/manage/' . $this->blog['id'], 'Group created', 'success');
        }
        else {
            $this->response->redirect('/', 'Could not insert to database', 'error');
        }
    }

    /**
     * Handles GET /contributors/editgroup
     */
    public function editgroup()
    {
        $groupID = $this->request->getUrlParameter(1);

        if (!$group = $this->modelGroups->getGroupById($groupID)) {
            $this->response->redirect('/', 'Group not found', 'error');
        }

        $this->blog = $this->modelBlogs->getBlogById($group['blog_id']);

        $this->checkUserAccess();

        BlogCMS::$blogID = $this->blog['id'];

        if ($this->request->method() == 'POST') {
            return $this->runEditGroup($group);
        }

        $this->response->setVar('blog', $this->blog);
        $this->response->setVar('group', $group);
        $this->response->setTitle('Edit contributors group');
        $this->response->write('contributors/editgroup.tpl');
    }

    protected function runEditGroup($group)
    {
        $permissions = $this->request->get('fld_permission');

        if (gettype($permissions) != 'array') {
            $this->response->redirect('/', 'Data error', 'error');
        }

        $systemPermissions = [
            "create_posts","publish_posts","edit_all_posts","delete_posts",
            "manage_comments","delete_files","change_settings","manage_contributors"
        ];

        $data = [];

        foreach ($systemPermissions as $permission) {
            if (array_key_exists($permission, $permissions) && $permissions[$permission] == 'on') {
                $data[$permission] = 1;
            }
            else {
                $data[$permission] = 0;
            }
        }

        $update = $this->modelGroups->update(['id' => $group['id']], [
            'name'        => $this->request->getString('fld_name'),
            'description' => $this->request->getString('fld_description'),
            'data'        => JSONHelper::arrayToJSON($data)
        ]);

        if ($update) {
            $this->response->redirect('/contributors/manage/' . $this->blog['id'], 'Group updated', 'success');
        }
        else {
            $this->response->redirect('/', 'Could not update database', 'error');
        }
    }

    /**
     * Handles POST /contributors/edit/<blogid>/<userid>
     * (via. edit)
     */
    protected function runUpdateContributor($contributor)
    {
        $groupID = $this->request->getInt('fld_group');

        // Check group exists and belongs to this blog
        if (!$group = $this->modelGroups->getGroupById($groupID)) {
            $this->response->redirect('/contributors/manage/' . $this->blog['id'], 'Group not found', 'error');
        }
        if ($group['blog_id'] != $this->blog['id']) {
            $this->response->redirect('/contributors/manage/' . $this->blog['id'], 'Group not found', 'error');
        }

        if($this->model->update(['user_id' => $contributor['id']], ['group_id' => $group['id']])) {
            $this->response->redirect('/contributors/manage/' . $this->blog['id'], 'Update successful', 'success');
        }
        else {
            $this->response->redirect('/contributors/manage/' . $this->blog['id'], 'Could not update contributor', 'error');
        }
    }
}
