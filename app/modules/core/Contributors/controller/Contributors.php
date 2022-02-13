<?php

namespace HamletCMS\Contributors\controller;

use HamletCMS\GenericController;
use rbwebdesigns\core\JSONHelper;
use HamletCMS\HamletCMS;
use rbwebdesigns\core\Email;

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

        $this->response->setBreadcrumbs([
            $this->blog->name => $this->blog->url(),
            'Contributors' => null
        ]);
        $this->response->headerIcon = 'users';
        $this->response->headerText = $this->blog->name . ': Manage contributors';

        $contributors = $this->model('contributors')->getBlogContributors($this->blog->id, true);
        $users = $this->model('useraccounts')->getByIds(array_column($contributors, 'user_id'));

        // Extract groups from user data
        array_walk($users, function($user) use ($contributors, $groups) {
            $contributorData = array_filter($contributors, function($contributor) use ($user) {
                return $contributor['user_id'] === $user->id;
            });
            $user->groupid = reset($contributorData)['group_id'];

            $groupData = array_filter($groups, function($group) use ($user) {
                return $group->id === $user->groupid;
            });
            $user->groupname = reset($groupData)->name;
        });

        $this->response->setVar('groups', $groups);
        $this->response->setVar('contributors', $users);
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

        $this->response->setBreadcrumbs([
            $this->blog->name => $this->blog->url(),
            'Contributors' => "/cms/contributors/manage/{$this->blog->id}",
            'Add contributor' => null
        ]);
        $this->response->headerIcon = 'user plus';
        $this->response->headerText = $this->blog->name . ': Create contributor';

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

        $this->response->setBreadcrumbs([
            $blog->name => $this->blog->url(),
            'Contributors' => "/cms/contributors/manage/{$blog->id}",
            'Invite' => null
        ]);
        $this->response->headerIcon = 'user plus';
        $this->response->headerText = $blog->name . ': Invite contributor';

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
            $this->response->routeRedirect('contributors.manage', 'Email or passwords did not match', 'error');
        }

        $checkUser = $this->model('useraccounts')->get('id', ['username' => $accountData['username']], '', '', false);
        if ($checkUser && $checkUser->id) {
            $this->response->routeRedirect('contributors.manage', 'Username is already taken', 'error');
        }

        if ($this->model('useraccounts')->register($accountData)) {
            // Get the user ID of the user just created
            $user = $this->model('useraccounts')->get('id', ['email' => $accountData['email']], '', '', false);
        }
        else {
            $this->response->routeRedirect('contributors.create', 'Error creating account', 'error');
        }

        if (!$this->model('contributors')->addBlogContributor($user->id, $blog->id, $groupID)) {
            $this->response->routeRedirect('contributors.create', 'Error assigning contributor', 'error');
        }

        $emailConfig = HamletCMS::config()['email'] ?? [];

        if ($emailConfig['enable'] ?? false) {
            $siteDomain = HamletCMS::config()['environment']['canonical_domain'];
            $email = new Email();
            $email->sender = $emailConfig['system_sender'];
            $email->recipient = $accountData['email'];
            $email->subject = 'You have been invited to contribute on ' . $blog->name;
            $email->message = "An account has been created for you, why not <a href='{$siteDomain}/cms'>login and create your first post</a>?";

            if (!$email->send()) {
                HamletCMS::session()->addMessage('Failed to send email', 'error');
            }
        }
        
        $this->response->routeRedirect('contributors.manage', 'Contributor created', 'success');
    }

    /**
     * Handles POST /contributors/invite/<blogid>
     */
    protected function runInvite()
    {
        $blog = HamletCMS::getActiveBlog();
        $userID = $this->request->getInt('selected_user', false);
        $groupID = $this->request->getInt('group', false);

        if (!$userID) {
            $this->response->routeRedirect('contributors.invite', 'User not found', 'error');
        }

        $user = $this->model('useraccounts')->getById($userID);

        if (!$user) {
            $this->response->routeRedirect('contributors.invite', 'User not found', 'error');
        }
        
        if (!$this->model('contributors')->addBlogContributor($user->id, $blog->id, $groupID)) {
            $this->response->routeRedirect('contributors.invite', 'Error assigning contributor', 'error');
        }

        $emailConfig = HamletCMS::config()['email'] ?? [];

        if ($emailConfig['enable'] ?? false) {
            $siteDomain = HamletCMS::config()['environment']['canonical_domain'];
            $email = new Email();
            $email->sender = $emailConfig['system_sender'];
            $email->recipient = $user->email;
            $email->subject = 'You have been invited to contribute on ' . $blog->name;
            $email->message = "You have been added as a contributor on {$blog->name}, why not <a href='{$siteDomain}/cms'>login and create your first post</a>?";

            if (!$email->send()) {
                HamletCMS::session()->addMessage('Failed to send email', 'error');
            }
        }

        $this->response->routeRedirect('contributors.manage', 'Contributor added', 'success');
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

        $this->response->setBreadcrumbs([
            $this->blog->name => $this->blog->url(),
            'Contributors' => "/cms/contributors/manage/{$this->blog->id}",
            $user->username => "/cms/account/user/{$user->id}",
            'Edit' => null
        ]);
        $this->response->headerIcon = 'user';
        $this->response->headerText = $this->blog->name . ': Edit contributor ' . $user->username;

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
            $this->response->routeRedirect('contributors.manage', 'Group not found', 'error');
        }
        if ($group->blog_id != $this->blog->id) {
            $this->response->routeRedirect('contributors.manage', 'Group not found', 'error');
        }

        if($this->model('contributors')->update(['user_id' => $contributor->id, 'blog_id' => $this->blog->id], ['group_id' => $group->id])) {
            $this->response->routeRedirect('contributors.manage', 'Update successful', 'success');
        }
        else {
            $this->response->routeRedirect('contributors.manage', 'Could not update contributor', 'error');
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
            $this->response->routeRedirect('contributors.manage', 'Contributor removed', 'success');
        }
        else {
            $this->response->routeRedirect('contributors.manage', 'Unable to remove contributor', 'error');
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

        $this->response->setBreadcrumbs([
            $this->blog->name => $this->blog->url(),
            'Contributors' => "/cms/contributors/manage/{$this->blog->id}",
            'Add group' => null
        ]);
        $this->response->headerIcon = 'user plus';
        $this->response->headerText = $this->blog->name . ': Create contributors group';

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
            $this->response->routeRedirect('contributors.manage', 'Group created', 'success');
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

        $this->response->setBreadcrumbs([
            $this->blog->name => $this->blog->url(),
            'Contributors' => "/cms/contributors/manage/{$this->blog->id}",
            'Edit group' => null
        ]);
        $this->response->headerIcon = 'users';
        $this->response->headerText = $this->blog->name . ': Edit contributors group &ldquo;' . $group->name . '&rdquo;';

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
            $this->response->routeRedirect('contributors.manage', 'Group updated', 'success');
        }
        else {
            $this->response->redirect('/cms', 'Could not update database', 'error');
        }
    }

}
