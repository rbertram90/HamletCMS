<?php
namespace rbwebdesigns\HamletCMS\UserAccounts\controller;

use rbwebdesigns\core\JSONHelper;
use rbwebdesigns\HamletCMS\GenericController;
use rbwebdesigns\HamletCMS\HamletCMS;

/**
 * Handles requests relating to user accounts.
 * 
 * @author Ricky Bertram <ricky@rbwebdesigns.co.uk>
 */
class UserAPI extends GenericController
{
    /**
     * @var \rbwebdesigns\HamletCMS\UserAccounts\model\UserAccounts
     */
    protected $model;
    /**
     * @var \rbwebdesigns\core\Request
     */
    protected $request;
    /**
     * @var \rbwebdesigns\HamletCMS\Response
     */
    protected $response;
    
    /**
     * Create an account controller instance
     */
    public function __construct()
    {
        $this->model = HamletCMS::model('\rbwebdesigns\HamletCMS\UserAccounts\model\UserAccounts');
        $this->request = HamletCMS::request();
        $this->response = HamletCMS::response();
    }
    
    /**
     * View a users profile summary card.
     * 
     * GET /api/user/get
     * 
     * Request parameters:
     *  username
     *  email
     */
    public function get()
    {
        $users = [];
        if ($username = $this->request->getString('username', false)) {
            $users = $this->model->get('*', ['username' => $username]);
        }
        elseif ($email = $this->request->getString('email', false)) {
            $users = $this->model->get('*', ['email' => $email]);
        }

        foreach ($users as $user) {
            unset($user->password);
            unset($user->security_q);
            unset($user->security_a);
        }
        
        $this->response->setBody(JSONhelper::arrayToJSON($users));
        $this->request->isAjax = true;
    }
    
}
