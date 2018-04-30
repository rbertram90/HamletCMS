<?php
namespace rbwebdesigns\blogcms;

use rbwebdesigns\core\Sanitize;

class AccountController extends GenericController
{
    protected $model;
    protected $request, $response;
    protected $modelComments;
    
    public function __construct()
    {
        $this->model = BlogCMS::model('\rbwebdesigns\blogcms\model\AccountFactory');
        $this->modelComments = BlogCMS::model('\rbwebdesigns\blogcms\model\Comments');

        $this->request = BlogCMS::request();
        $this->response = BlogCMS::response();
    }
    
    /**
     * Handles /account/user/[<userid>]
     */
    public function user()
    {
        if ($userID = $this->request->getUrlParameter(1)) {
            if($user = $this->model->getById($userID)) {
                // Found user
            }
            else {
                $this->response->redirect('/cms', 'User not found', 'error');
            }
        }
        else {
            $userID = BlogCMS::session()->currentUser['id'];
            $user = $this->model->getById($userID);
        }

        $this->response->setVar('comments', $this->modelComments->getCommentsByUser($userID, 0));
        
        $this->response->setVar('user', $user);
        $this->response->setTitle($user['username'] . '\'s profile');
        $this->response->write('account/viewuser.tpl');
    }

    /**
     * Handles GET /account/login
     */
    public function login(&$request, &$response)
    {
        if($request->method() == 'POST') return $this->runLogin($request, $response);

        $response->setTitle('Login required');
        $response->writeTemplate('account/login.tpl');
    }

    /**
     * Handles GET /account/register
     */
    public function register(&$request, &$response)
    {
        if($request->method() == 'POST') return $this->runRegister($request, $response);

        $response->setTitle('Create a new account');
        $response->writeTemplate('account/register.tpl');
    }

    /**
     * Handles POST /account/login
     */
    protected function runLogin(&$request, &$response)
    {
        $username = $request->getString('fld_username');
        $password = $request->getString('fld_password');

        if (strlen($username) == 0 || strlen($password) == 0) {
            $response->redirect('/cms/account/login', 'Please complete all fields', 'error');
        }

        if ($this->model->login($username, $password)) {
            $response->redirect('/cms', 'Welcome back', 'success');
        }
        else {
            $response->redirect('/cms/account/login', 'No match found for username and password', 'error');
        }
    }

    /**
     * Handles POST /account/register
     */
    protected function runRegister(&$request, &$response)
    {
        $details = [
            'firstname' => $request->getString('fld_name'),
            'surname' => $request->getString('fld_surname'),
            'email' => $request->getString('fld_email'),
            'emailConfirm' => $request->getString('fld_email_2'),
            'username' => $request->getString('fld_username'),
            'password' => $request->getString('fld_password'),
            'passwordConfirm' => $request->getString('fld_password_2')
        ];

        // Check all fields complete
        foreach ($details as $value) {
            if (strlen($value) == 0) {
                $response->redirect('/cms/account/login', 'Please complete all fields', 'error');
                break;
            }
        }

        // Validate
        if ($details['email'] != $details['emailConfirm'] || $details['password'] != $details['passwordConfirm']) {
            $response->redirect('/cms/account/register', 'Email or passwords did not match', 'error');
        }

        if ($this->model->register($details)) {
            $response->redirect('/cms/account/login', 'Account created', 'success');
        }
        else {
            $response->redirect('/cms/account/login', 'Unable to create account right now - please try again later', 'error');
        }
    }

    /**
     * Handles GET /account/logout
     */
    public function logout(&$request, &$response)
    {
        $session = BlogCMS::session();
        $session->delete('user');
        $session->end();
        
        $response->redirect('/', 'Logout successful', 'success');
    }

    /**
     * Handles GET /account/settings
     */
    public function settings()
    {
        if($this->request->method() == 'POST') return $this->saveAccountSettings();

        $this->response->setVar('user', $this->model->getById(BlogCMS::session()->currentUser['id']));
        $this->response->setTitle('Profile settings');
        $this->response->write('account/editdetails.tpl');
    }

    /**
     * Handles POST /account/settings
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
        
        if ($this->model->saveSettings($details)) {
            $this->response->redirect('/cms/account/settings', 'Account updated', 'success');
        }
        else {
            $this->response->redirect('/cms/account/settings', 'Unable to save profile', 'error');
        }
    }

    /**
     * Handles GET /account/password
     */
    public function password()
    {
        if ($this->request->method() == 'POST') return $this->updatePassword();
        
        $this->response->setTitle('Change Password');
        // $this->view->setVar();
        $this->response->write('account/changepassword.tpl');
    }

    /**
     * Handles POST /account/password
     */
    protected function updatePassword()
    {
        // Change Password
        $details = array(
            "current_password" => Sanitize::string($_POST['fld_current_password']),
            "new_password" => Sanitize::string($_POST['fld_new_password']),
            "new_password_rpt" => Sanitize::string($_POST['fld_new_password_rpt'])
        );
        
        // Update DB
        if($this->mdlUsers->updatePassword($details)) setSystemMessage("Password changed", "Success");
        else setSystemMessage("Failed to change password", "Error");
            
        redirect('/account/changepassword');
    }
    
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
     * Handles GET /account/avatar
     */
    public function avatar()
    {
        if ($this->request->method() == 'POST') return $this->updateAvatar();

        $this->response->setTitle('Upload profile photo');
        $this->response->write('account/uploadavatar.tpl');
    }

    /**
     * Handles POST /account/avatar
     */
    protected function updateAvatar()
    {
        // If file type is correct type and is less than 20kb
        /*
        ($_FILES["file"]["type"] == "image/gif")|| || ($_FILES["file"]["type"] == "image/pjpeg") || ($_FILES["file"]["type"] == "image/png")
        RESTRICTED TO JPG
        */
        $userID = BlogCMS::session()->currentUser['id'];
        // $user = $this->model->getById($userID);

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
        
        $this->model->update(['id' => $userID], [
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
        
        $this->response->redirect('/cms/account/avatar', 'Upload Successful', 'Success');
    }
}

