<?php
namespace rbwebdesigns\blogcms;

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
     * @param string $username
     * @param string $password
     * 
     * @return bool
     *  Is the login successful?
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
}