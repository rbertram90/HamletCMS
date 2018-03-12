<?php
namespace rbwebdesigns\blogcms\model;

use rbwebdesigns\core\Sanitize;
use rbwebdesigns\core\model\RBFactory;

/**
 * /app/model/mdl_user.inc.php
 */
class AccountFactory extends RBFactory
{
    protected $tableName = 'users';
    protected $database;

    /**
     * Check username and password are a match in database
     * and set session flags to log the user in
     * 
     * @param string $username
     * @param string $password
     * 
     * @return bool
     *  Was the login successful?
     * 
     * @todo Stop using md5 hashing for passwords
     */
    public function login($username, $password)
    {
        $user = $this->get(['id', 'password', 'admin'], ['username' => $username ,'password' => md5($password)], '', '', false);

        if($user) {
            // Log the user in
            BlogCMS::session()->setCurrentUser([
                'id' => $user['id'],
                'admin' => $user['admin'],
            ]);
            return true;
        }
        else {
            return false;
        }
    }

    /**
     * Get a user record from database by id
     * 
     * @param int $userID
     * 
     * @return array
     */
    public function getById($userID)
    {
        return $this->get(['*'], ['id' => $userID], '', '', false);
    }
}