<?php
namespace HamletCMS\UserAccounts\controller;

use HamletCMS\GenericController;
use HamletCMS\HamletCMS;
use rbwebdesigns\core\Email;

/**
 * Handles requests relating to user accounts.
 * 
 * @author Ricky Bertram <ricky@rbwebdesigns.co.uk>
 */
class UserAccounts extends GenericController
{
    /**
     * View a users profile.
     * 
     * GET /account/user/[userid]
     */
    public function user()
    {
        if ($userID = $this->request->getUrlParameter(1)) {}
        else {
            $userID = HamletCMS::session()->currentUser['id'];
        }

        $user = $this->model('useraccounts')->getById($userID);

        if (!$user) {
            $this->response->redirect('/cms', 'User not found', 'error');
        }

        $numberOfPost = $this->model('posts')->count(['author_id' => $userID]);
        $this->response->setVar('postCount', $numberOfPost);

        $contributedBlogs = $this->model('contributors')->getContributedBlogs($userID);
        $this->response->setVar('blogCount', count($contributedBlogs));
        $this->response->setVar('blogs', $contributedBlogs);

        $dynamicContent = "";
        HamletCMS::runHook('content', ['key' => 'userProfile', 'user' => $user, 'content' => &$dynamicContent]);
        $this->response->setVar('dynamicContent', $dynamicContent);

        $this->response->setVar('user', $user);
        $this->response->setTitle($user->username . '\'s profile');
        $this->response->write('viewuser.tpl', 'UserAccounts');
    }

    /**
     * View the login form.
     * 
     * GET /account/login
     */
    public function login()
    {
        if ($this->request->method() == 'POST') return $this->runLogin();

        $config = HamletCMS::config();

        $this->response->setVar('registerAllowed', $config['users']['allow_anon_registration']);
        $this->response->setTitle('Login required');
        $this->response->writeTemplate('login.tpl', 'UserAccounts');
    }

    /**
     * View the user registration form.
     * 
     * GET /account/register
     */
    public function register()
    {
        $config = HamletCMS::config();

        // Check if anonymous user registrations have been disabled
        if (!$config['users']['allow_anon_registration']) {
            $this->response->redirect('/');
        }

        if($this->request->method() == 'POST') return $this->runRegister();

        $this->response->setTitle('Create a new account');
        $this->response->writeTemplate('register.tpl', 'UserAccounts');
    }

    /**
     * GET /account/resetpassword
     */
    public function resetpassword()
    {
        if ($this->request->method() == 'POST') {
            return $this->runResetPassword();
        }

        $email = $this->request->get('email', false);
        $token = $this->request->get('token', false);

        if ($email && $token) {
            /** @var \HamletCMS\UserAccounts\User */
            $user = $this->model('useraccounts')->get('*', [
                'email' => $email,
            ], '', '', false);

            if ($user->reset_token === $token) {
                $this->response->setVar('email', $email);
                $this->response->setVar('token', $token);
                $this->response->setTitle('Reset your password');
                $this->response->writeTemplate('resetpassword.tpl', 'UserAccounts');
                return;
            }
        }

        $this->response->setTitle('Reset your password');
        $this->response->writeTemplate('resetpassword.tpl', 'UserAccounts');
    }

    /**
     * Process the reset password form.
     * 
     * POST /account/resetpassword
     */
    protected function runResetPassword()
    {
        $email = $this->request->getString('email');
        $token = $this->request->getString('token');
        $password = $this->request->getString('password');
        $password_repeat = $this->request->getString('password_repeat');

        /** @var \HamletCMS\UserAccounts\User */
        $user = $this->model('useraccounts')->get('*', [
            'email' => $email
        ], '', '', false);

        if (!$user) {
            $this->response->redirect('/cms/account/resetpassword', 'Account details incorrect', 'error');
        }

        if ($token && $token === $user->reset_token) {
            if (strlen($password) < 3 || $password !== $password_repeat) {
                $this->response->redirect('/cms/account/resetpassword?email='.$email.'&token='.$token, 'Please check your input.', 'error');
            }

            $hashpassword = $this->model('useraccounts')->hashPassword($password);
            $update = $this->model('useraccounts')->update(['id' => $user->id], [
                'password' => $hashpassword,
                'reset_token' => '',
            ]);

            if (!$update) {
                $this->response->redirect('/cms/account/resetpassword', 'Failed to save new password', 'error');
            }

            $this->response->redirect('/cms/account/login', 'New password saved, you can now login.', 'success');
        }

        $emailConfig = HamletCMS::config()['email'] ?? [];
        $siteDomain = HamletCMS::config()['environment']['canonical_domain'];

        $resetUpdate = $user->setResetPasswordToken();
       
        if ($resetUpdate) {
            $resetEmail = new Email();
            $resetEmail->recipient = $email;
            $resetEmail->subject = 'Reset your password - HamletCMS';
            $resetEmail->message = 'A password reset has been requested on your account, click <a href="'.$siteDomain.'/cms/account/resetpassword?email='.$email.'&token='.$user->reset_token.'">this link</a> to reset your password.';
            $resetEmail->sender = $emailConfig['system_sender'];

            if ($resetEmail->send()) {
                $this->response->redirect('/cms/account/login', 'Email has been sent with a link to reset your password.', 'success');
            }
        }

        $this->response->redirect('/cms/account/resetpassword', 'Unable to send the reset email, please try again later.', 'error');
    }

    /**
     * Process the login form
     * 
     * POST /account/login
     */
    protected function runLogin()
    {
        $username = $this->request->getString('fld_username');
        $password = $this->request->getString('fld_password');

        if (strlen($username) == 0 || strlen($password) == 0) {
            $this->response->redirect('/cms/account/login', 'Please complete all fields', 'error');
        }

        if ($this->model('useraccounts')->login($username, $password)) {
            HamletCMS::runHook('onAccountLogin', []);

            $this->response->redirect('/cms', 'Welcome back', 'success');
        }
        else {
            $this->response->redirect('/cms/account/login', 'No match found for username and password', 'error');
        }
    }

    /**
     * Process the register user form.
     * 
     * POST /account/register
     */
    protected function runRegister()
    {
        $details = [
            'firstname'       => $this->request->getString('fld_name'),
            'surname'         => $this->request->getString('fld_surname'),
            'email'           => $this->request->getString('fld_email'),
            'emailConfirm'    => $this->request->getString('fld_email_2'),
            'username'        => $this->request->getString('fld_username'),
            'password'        => $this->request->getString('fld_password'),
            'passwordConfirm' => $this->request->getString('fld_password_2')
        ];

        // Check all fields complete
        foreach ($details as $value) {
            if (strlen($value) == 0) {
                $this->response->redirect('/cms/account/login', 'Please complete all fields', 'error');
            }
        }

        // Validate
        if ($details['email'] != $details['emailConfirm'] || $details['password'] != $details['passwordConfirm']) {
            $this->response->redirect('/cms/account/register', 'Email or passwords did not match', 'error');
        }

        if ($this->model('useraccounts')->register($details)) {
            HamletCMS::runHook('onAccountCreated', []);
            $this->response->redirect('/cms/account/login', 'Account created', 'success');
        }
        else {
            $this->response->redirect('/cms/account/login', 'Unable to create account right now - please try again later', 'error');
        }
    }

    /**
     * Run session logout.
     * 
     * GET /account/logout
     */
    public function logout()
    {
        $session = HamletCMS::session();
        $session->delete('user');
        $session->end();
        
        $this->response->redirect('/', 'Logout successful', 'success');
    }

    /**
     * View the account settings page.
     * 
     * GET /account/settings
     */
    public function settings()
    {
        if($this->request->method() == 'POST') return $this->saveAccountSettings();

        $this->response->setVar('user', $this->model('useraccounts')->getById(HamletCMS::session()->currentUser['id']));
        $this->response->setTitle('Profile settings');
        $this->response->write('editdetails.tpl', 'UserAccounts');
    }

    /**
     * Process an account settings update.
     * 
     * POST /cms/account/settings
     */
    protected function saveAccountSettings()
    {
        $details = [
            "name"        => $this->request->getString('fld_firstname'),
            "surname"     => $this->request->getString('fld_surname'),
            "description" => $this->request->getString('fld_description'),
            "email"       => $this->request->getString('fld_email'),
            "gender"      => $this->request->getString('fld_gender'),
            "location"    => $this->request->getString('fld_location'),
            "username"    => $this->request->getString('fld_username'),
        ];
        
        // Sanitize Date Input
        $in_dob_day   = $this->request->getInt('fld_dob_day');
        $in_dob_month = $this->request->getInt('fld_dob_month');
        $in_dob_year  = $this->request->getInt('fld_dob_year');
        
        if(checkdate($in_dob_month, $in_dob_day, $in_dob_year)) {
            $details['dob'] = date("Y-m-d", strtotime($in_dob_year."-".$in_dob_month."-".$in_dob_day));
        }
        else {
            $this->response->redirect('/cms/account/settings', 'Invalid date of birth', 'error');
        }
        
        if ($this->model('useraccounts')->saveSettings($details)) {
            HamletCMS::runHook('onAccountUpdated', []);
            $this->response->redirect('/cms/account/settings', 'Account updated', 'success');
        }
        else {
            $this->response->redirect('/cms/account/settings', 'Unable to save profile', 'error');
        }
    }

    /**
     * View the password change form.
     * 
     * GET /account/password
     */
    public function password()
    {
        if ($this->request->method() == 'POST') return $this->updatePassword();
        
        $this->response->setTitle('Change Password');
        // $this->view->setVar();
        $this->response->write('changepassword.tpl', 'UserAccounts');
    }

    /**
     * Process the update password form.
     * 
     * POST /account/password
     */
    protected function updatePassword()
    {
        // Change Password
        $details = [
            "current_password" => $this->request->getString('fld_current_password'),
            "new_password" => $this->request->getString('fld_new_password'),
            "new_password_rpt" => $this->request->getString('fld_new_password_rpt')
        ];
        
        if ($details['new_password'] !== $details['new_password_rpt']) {
            $this->response->redirect('/cms/account/password', 'Passwords did not match', 'error');
        }

        $user = HamletCMS::session()->currentUser;
        $current = $this->model('useraccounts')->get('password', ['id' => $user['id']], '', '', false);

        if (!$user || !password_verify($details['current_password'], $current['password'])) {
            $this->response->redirect('/cms/account/password', 'Unable to verify current password', 'error');
        }

        $newPassword = password_hash($details['new_password'], PASSWORD_DEFAULT);

        if (!$this->model('useraccounts')->update(['id' => $user['id']], ['password' => $newPassword])) {
            $this->response->redirect('/cms/account/password', 'Failed to save new password', 'error');
        }
        
        HamletCMS::runHook('onPasswordChanged', []);
        $this->response->redirect('/cms/account/password', 'Password changed', 'success');
    }
    
    /**
     * Generate a random photo name.
     * 
     * @todo Move out of this class
     */
    protected function generateProfilePhotoName()
    {
        $filename = rand(10000,32000) . rand(10000,32000) . "." . pathinfo($_FILES["avatar"]["name"], PATHINFO_EXTENSION);
        
        if (file_exists(SERVER_AVATAR_FOLDER . "/" . $filename))
        {
            return $this->generateProfilePhotoName(); // run recursive until we've got a unique one?
        }
        
        return $filename;
    }

    /**
     * View the change profile photo page.
     * 
     * GET /account/avatar
     */
    public function avatar()
    {
        if ($this->request->method() == 'POST') return $this->updateAvatar();

        $this->response->setTitle('Upload profile photo');
        $this->response->write('uploadavatar.tpl', 'UserAccounts');
    }

    /**
     * Process change profile photo form.
     * 
     * POST /account/avatar
     */
    protected function updateAvatar()
    {
        // If file type is correct type and is less than 20kb
        /*
        ($_FILES["file"]["type"] == "image/gif")|| || ($_FILES["file"]["type"] == "image/pjpeg") || ($_FILES["file"]["type"] == "image/png")
        RESTRICTED TO JPG
        */
        $userID = HamletCMS::session()->currentUser['id'];

        if (!($_FILES['avatar']['type'] == 'image/jpeg' && $_FILES['avatar']['size'] < 200000)) {
            $this->response->redirect('/cms/account/avatar', 'Unsuitable Photo - file must be a JPEG image under 20KB', 'error');
        }
        
        // file has upload error then return error
        if ($_FILES['avatar']['error'] > 0) {
            $this->response->redirect('/cms/account/avatar', 'Unable to upload - ' . $_FILES['avatar']['error'], 'error');
        }
        
        // Make a new file name (to hopefully avoid duplicates)
        $_FILES['avatar']['name'] = $this->generateProfilePhotoName();
        
        move_uploaded_file($_FILES['avatar']['tmp_name'], SERVER_AVATAR_FOLDER . '/' . $_FILES['avatar']['name']);
        
        $this->model('useraccounts')->update(['id' => $userID], [
            'profile_picture' => $_FILES['avatar']["name"]
        ]);

        // Make a thumbnail image
        $imagePath = SERVER_AVATAR_FOLDER . '/' . $_FILES["avatar"]["name"];
        $srcImage = imagecreatefromjpeg($imagePath);
        list($imageHeight,$imageWidth) = getimagesize($imagePath);

        $min = min([$imageHeight, $imageWidth]);

        if($min == $imageWidth) {
            $startX = floor(($imageHeight - $imageWidth) / 2);
            $startY = 0;
        }
        else {
            $startX = 0;
            $startY = floor(($imageWidth - $imageHeight) / 2);
        }

        $destImage = imagecreatetruecolor($min , $min);
        $destLoc = SERVER_AVATAR_FOLDER . '/thumbs/' . $_FILES["avatar"]["name"];
        imagecopy($destImage, $srcImage , 0 , 0 , $startX , $startY , $min , $min);
        imagejpeg($destImage, $destLoc);
        
        HamletCMS::runHook('onAvatarChanged', []);
        $this->response->redirect('/cms/account/avatar', 'Upload Successful', 'Success');
    }

    /**
     * View a users profile summary card.
     * 
     * GET /account/card/<userid>
     */
    public function card()
    {
        $userID = $this->request->getUrlParameter(1);

        if (!$user = $this->model('useraccounts')->getById($userID)) {
            $this->response->write('No user found');
            die();
        }

        $this->response->setVar('user', $user);

        $this->request->isAjax = true;
        $this->response->write('usercard.tpl', 'UserAccounts');
    }
    
    /**
     * GET /cms/users/admin
     */
    public function usersAdmin()
    {
        $sort = $this->request->getString('sort', '');
        $this->response->setTitle('User accounts login');
        $this->response->setVar('users', $this->model('useraccounts')->getAll($sort));
        $this->response->write('admintable.tpl', 'UserAccounts');
    }

}
