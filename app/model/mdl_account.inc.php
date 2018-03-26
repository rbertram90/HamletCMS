<?php
namespace rbwebdesigns\blogcms\model;

use rbwebdesigns\blogcms\BlogCMS;
use rbwebdesigns\core\Sanitize;
use rbwebdesigns\core\model\RBFactory;

/**
 * /app/model/mdl_account.inc.php
 */
class AccountFactory extends RBFactory
{
    /**
     * @var string Database table name for this model
     */
    protected $tableName = 'users';
    
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
     * Create a new user
     * 
     * @param array $details
     * 
     * @todo throw exception if username is already taken
     * @todo not use md5 for hashing
     */
    public function register($details)
    {
        if($this->count(['username' => $details['username']]) > 0) {
            return false;
        }

        $data = [
            'name' => $details['firstname'],
            'surname' => $details['surname'],
            'username' => $details['username'],
            'password' => md5($details['password']),
            'email' => $details['email'],
            'admin' => 0,
            'signup_date' => date('Y-m-d H:i:s')
        ];

        if (isset($details['gender'])) $data['gender'] = $details['gender'];

        return $this->insert($data);
    }

    /**
     * Get a user record from database by id
     * 
     * @param int $userID
     * 
     * @return array|bool
     *   Returns false if user is not found
     */
    public function getById($userID)
    {
        return $this->get(['*'], ['id' => $userID], '', '', false);
    }

    /**
     * Save account settings
     * 
     * @param array $fields
     *   User data - keyed by database column names
     */
    public function saveSettings($fields)
    {        
        $user = BlogCMS::session()->currentUser;

        return $this->update(['id' => $user['id']], $fields);
    }
}