<?php

namespace HamletCMS\Contributors\controller;

use HamletCMS\GenericController;
use rbwebdesigns\core\JSONHelper;
use HamletCMS\HamletCMS;

/**
 * /app/controller/contributors_controller.inc.php
 * 
 * @author R Bertram <ricky@rbwebdesigns.co.uk>
 */
class Contributors extends GenericController
{
    /** @var \HamletCMS\Blog\Blog */
    protected $blog;
    
    public function __construct()
    {
        parent::__construct();
        $this->setup();
    }

    /**
     * Setup common objects
     * Checks the user has permissions to run the request
     */
    protected function setup()
    {
        if (!$this->blog = HamletCMS::getActiveBlog()) {
            // Danger - need to handle permissions seperately!
            return;
        }

        $this->checkUserAccess();
        HamletCMS::$activeMenuLink = '/cms/contributors/manage/'. $this->blog->id;
    }

    /**
     * Check the user has access to view/change the contributors of the blog
     * Requires $this->blog to be set, redirects if not got permission
     */
    protected function checkUserAccess()
    {
        $access = true;
        if (!$this->blog) {
            $access = false;
        }
        elseif (!$this->model('permissions')->userHasPermission('manage_contributors', $this->blog->id)) {
            $access = false;
        }

        if (!$access) {
            $this->response->redirect('/cms', '403 Access Denied', 'error');
        }
    }
    
    /**
     * Handles /contributors/manage/<blogid>
     */
    public function manage()
    {
        $groups = $this->model('contributorgroups')->get('*', ['blog_id' => $this->blog->id]);

        if (count($groups) == 0) {
            $this->model('contributorgroups')->createDefaultGroups($this->blog->id);
            $groups = $this->model('contributorgroups')->get('*', ['blog_id' => $this->blog->id]);
        }

        $this->response->setVar('groups', $groups);
        $this->response->setVar('contributors', $this->model('contributors')->getBlogContributors($this->blog->id));
        $this->response->setVar('blog', $this->blog);
        $this->response->setTitle('Manage Blog Contributors - '. $this->blog->name);
        $this->response->write('manage.tpl', 'Contributors');
    }
    
    /**
     * Handles /contributors/create/<blogid>
     */
    public function create()
    {
        if ($this->request->method() == 'POST') return $this->runCreate();

        $blog = HamletCMS::getActiveBlog();
        $groups = $this->model('contributorgroups')->get('*', ['blog_id' => $blog->id]);

        $this->response->setVar('blog', $blog);
        $this->response->setVar('groups', $groups);
        $this->response->setTitle('Create Contributor');
        $this->response->write('create.tpl', 'Contributors');
    }

    /**
     * Handles /contributors/invite/<blogid>
     */
    public function invite()
    {
        if ($this->request->method() == 'POST') return $this->runInvite();
        $blog = HamletCMS::getActiveBlog();
        $groups = $this->model('contributorgroups')->get('*', ['blog_id' => $blog->id]);
        $this->response->setVar('blog', $blog);
        $this->response->setVar('groups', $groups);
        $this->response->setTitle('Invite Contributor');
        $this->response->write('invite.tpl', 'Contributors');
    }

    /**
     * Handles POST /contributors/create/<blogid>
     */
    protected function runCreate()
    {
        $blog = HamletCMS::getActiveBlog();
        
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

        $groupID = $this->request->getInt('group', false);

        // Validate
        if ($accountData['email'] != $accountData['emailConfirm']
            || $accountData['password'] != $accountData['passwordConfirm']) {
            $this->response->redirect('/cms/contributors/manage', 'Email or passwords did not match', 'error');
        }

        $checkUser = $this->model('useraccounts')->get('id', ['username' => $accountData['username']], '', '', false);
        if ($checkUser && $checkUser->id) {
            $this->response->redirect('/cms/contributors/manage', 'Username is already taken', 'error');
        }

        if ($this->model('useraccounts')->register($accountData)) {
            // Get the user ID of the user just created
            $user = $this->model('useraccounts')->get('id', ['email' => $accountData['email']], '', '', false);
        }
        else {
            $this->response->redirect('/cms/contributors/create/' . $blog->id, 'Error creating account', 'error');
        }

        if (!$this->model('contributors')->addBlogContributor($user->id, $blog->id, $groupID)) {
            $this->response->redirect('/cms/contributors/create/' . $blog->id, 'Error assigning contributor', 'error');
        }

        $this->response->redirect('/cms/contributors/manage/' . $blog->id, 'Contributor created', 'success');
    }

    /**
     * Handles POST /contributors/invite/<blogid>
     */
    protected function runInvite()
    {
        $blog = HamletCMS::getActiveBlog();

        $userID = $this->request->getInt('selected_user', false);
        $groupID = $this->request->getInt('group', false);

        if (!$userID) $this->response->redirect('/cms/contributors/invite/'. $blog->id, 'User not found', 'error');

        $user = $this->model('useraccounts')->get('id', ['id' => $userID], '', '', false);

        if (!$user) $this->response->redirect('/cms/contributors/invite/'. $blog->id, 'User not found', 'error');
        
        if (!$this->model('contributors')->addBlogContributor($user->id, $blog->id, $groupID)) {
            $this->response->redirect('/cms/contributors/invite/'. $blog->id, 'Error assigning contributor', 'error');
        }
        
        $this->response->redirect('/cms/contributors/manage/'. $blog->id, 'Contributor added', 'success');
    }
    
    /**
     * Handles /contributors/edit/<blogid>/<userid>
     */
    public function edit()
    {
        $contributorID = $this->request->getUrlParameter(2);

        if (!$user = $this->model('useraccounts')->getById($contributorID)) {
            $this->response->redirect('/cms', 'Unable to find contributor', 'error');
        }

        if ($this->request->method() == 'POST') {
            return $this->runUpdateContributor($user);
        }

        $this->response->setVar('contributor', $user);
        $this->response->setVar('blog', $this->blog);
        $this->response->setTitle('Edit contributor - '. $user->name);
        $this->response->setVar('groups', $this->model('contributorgroups')->get('*', ['blog_id' => $this->blog->id]));
        $this->response->write('edit.tpl', 'Contributors');
    }
    
    /**
     * Handles POST /contributors/edit/<blogid>/<userid>
     * (via. edit)
     */
    protected function runUpdateContributor($contributor)
    {
        $groupID = $this->request->getInt('fld_group');

        // Check group exists and belongs to this blog
        if (!$group = $this->model('contributorgroups')->getGroupById($groupID)) {
            $this->response->redirect('/cms/contributors/manage/'. $this->blog->id, 'Group not found', 'error');
        }
        if ($group->blog_id != $this->blog->id) {
            $this->response->redirect('/cms/contributors/manage/'. $this->blog->id, 'Group not found', 'error');
        }

        if($this->model('contributors')->update(['user_id' => $contributor->id, 'blog_id' => $this->blog->id], ['group_id' => $group->id])) {
            $this->response->redirect('/cms/contributors/manage/'. $this->blog->id, 'Update successful', 'success');
        }
        else {
            $this->response->redirect('/cms/contributors/manage/'. $this->blog->id, 'Could not update contributor', 'error');
        }
    }

    /**
     * Handles /contributors/remove/<blogid>/<userid>
     * Confirmation happened client side
     */
    public function remove()
    {
        $contributorID = $this->request->getUrlParameter(2);

        if (!$user = $this->model('useraccounts')->getById($contributorID)) {
            $this->response->redirect('/cms', 'Unable to find contributor', 'error');
        }

        if ($this->model('contributors')->delete(['blog_id' => $this->blog->id, 'user_id' => $contributorID])) {
            $this->response->redirect('/cms/contributors/manage/'. $this->blog->id, 'Contributor removed', 'success');
        }
        else {
            $this->response->redirect('/cms/contributors/manage/'. $this->blog->id, 'Unable to remove contributor', 'error');
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
        $this->response->setVar('permissions', $this->model('permissions')::getList());
        $this->response->setTitle('Add contributors group');
        $this->response->write('creategroup.tpl', 'Contributors');
    }

    /**
     * Handles GET /contributrs/creategroup/<blogid>
     */
    protected function runCreateGroup()
    {
        $permissions = $this->request->get('fld_permission');

        if (gettype($permissions) != 'array') {
            $this->response->redirect('/cms', 'Data error', 'error');
        }

        $permissionsList = [
            "create_posts","publish_posts","edit_all_posts","delete_posts",
            "delete_files","change_settings","manage_contributors"
        ];

        $cache = HamletCMS::getCache('permissions');
        foreach ($cache as $permission) {
            $permissionsList[] = $permission['key'];
        }

        $data = [];

        foreach ($permissionsList as $permission) {
            if (array_key_exists($permission, $permissions) && $permissions[$permission] == 'on') {
                $data[$permission] = 1;
            }
            else {
                $data[$permission] = 0;
            }
        }

        $insert = $this->model('contributorgroups')->insert([
            'blog_id'     => $this->blog->id,
            'name'        => $this->request->getString('fld_name'),
            'description' => $this->request->getString('fld_description'),
            'data'        => JSONHelper::arrayToJSON($data),
            'locked'      => 0
        ]);

        if ($insert) {
            $this->response->redirect('/cms/contributors/manage/'. $this->blog->id, 'Group created', 'success');
        }
        else {
            $this->response->redirect('/cms', 'Could not insert to database', 'error');
        }
    }

    /**
     * Handles GET /contributors/editgroup
     */
    public function editgroup()
    {
        $groupID = $this->request->getUrlParameter(1);

        if (!$group = $this->model('contributorgroups')->getGroupById($groupID)) {
            $this->response->redirect('/cms', 'Group not found', 'error');
        }

        $this->blog = $this->model('blogs')->getBlogById($group->blog_id);

        $this->checkUserAccess();

        HamletCMS::$blogID = $this->blog->id;

        if ($this->request->method() == 'POST') {
            return $this->runEditGroup($group);
        }

        $this->response->setVar('permissions', $this->model('permissions')->getList());
        $this->response->setVar('blog', $this->blog);
        $this->response->setVar('group', $group);
        $this->response->setTitle('Edit contributors group');
        $this->response->write('editgroup.tpl', 'Contributors');
    }

    protected function runEditGroup($group)
    {
        $permissions = $this->request->get('fld_permission');

        if (gettype($permissions) != 'array') {
            $this->response->redirect('/cms', 'Data error', 'error');
        }

        $permissionsList = [];

        $cache = HamletCMS::getCache('permissions');
        foreach ($cache as $permission) {
            $permissionsList[] = $permission['key'];
        }

        $data = [];

        foreach ($permissionsList as $permission) {
            if (array_key_exists($permission, $permissions) && $permissions[$permission] == 'on') {
                $data[$permission] = 1;
            }
            else {
                $data[$permission] = 0;
            }
        }

        $update = $this->model('contributorgroups')->update(['id' => $group->id], [
            'name'        => $this->request->getString('fld_name'),
            'description' => $this->request->getString('fld_description'),
            'data'        => JSONHelper::arrayToJSON($data)
        ]);

        if ($update) {
            $this->response->redirect('/cms/contributors/manage/'. $this->blog->id, 'Group updated', 'success');
        }
        else {
            $this->response->redirect('/cms', 'Could not update database', 'error');
        }
    }

}
