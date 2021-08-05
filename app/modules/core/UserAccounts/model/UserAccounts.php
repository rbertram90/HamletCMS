<?php
namespace HamletCMS\UserAccounts\model;

use HamletCMS\HamletCMS;
use rbwebdesigns\core\Sanitize;
use rbwebdesigns\core\model\RBFactory;

/**
 * /app/model/mdl_account.inc.php
 */
class UserAccounts extends RBFactory
{
    /** @var string Database table name for this model */
    protected $tableName = 'users';

    protected $passwordHash = '';
    protected $subClass = '\\HamletCMS\\UserAccounts\\User';
    
    /**
     * Check username and password are a match in database
     * and set session flags to log the user in
     * 
     * @param string $username
     * @param string $password
     * 
     * @return bool
     *  Was the login successful?
     */
    public function login($username, $password)
    {
        $user = $this->get(['id', 'password', 'admin'], ['username' => $username], '', '', false);

        if ($user && password_verify($password, $user->password)) {
            
            if (password_needs_rehash($user->password, PASSWORD_DEFAULT)) {
                // If so, create a new hash, and replace the old one
                $newHash = password_hash($user->password, PASSWORD_DEFAULT);
                $this->update(['id' => $user->id], ['password' => $newHash]);
            }

            // Update the last login
            $this->update(['id' => $user->id], ['last_login' => date('Y-m-d H:i:s')]);

            // Log the user in
            HamletCMS::session()->setCurrentUser([
                'id' => $user->id,
                'admin' => $user->admin,
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
     */
    public function register($details)
    {
        if ($this->count(['username' => $details['username']]) > 0) {
            return false;
        }

        $data = [
            'name' => $details['firstname'],
            'surname' => $details['surname'],
            'username' => $details['username'],
            'password' => password_hash($details['password'], PASSWORD_DEFAULT),
            'email' => $details['email'],
            'signup_date' => date('Y-m-d H:i:s'),
            'admin' => array_key_exists('admin', $details) ? $details['admin'] : 0
        ];

        if (isset($details['gender'])) {
            $data['gender'] = $details['gender'];
        }

        return $this->insert($data);
    }

    /**
     * Get a user record from database by id
     * 
     * @param int $userID
     * 
     * @return \HamletCMS\UserAccounts\User|bool
     *   Returns false if user is not found
     */
    public function getById($userID)
    {
        return $this->get(['*'], ['id' => $userID], '', '', false);
    }

    /**
     * Get multiple records by ID
     */
    public function getByIds($userIDs)
    {
        return $this->db->query("SELECT * FROM {$this->tablename} WHERE id IN " . implode(',', $userIDs));
    }

    /**
     * Save account settings
     * 
     * @param array $fields
     *   User data - keyed by database column names
     */
    public function saveSettings($fields)
    {        
        $user = HamletCMS::session()->currentUser;

        return $this->update(['id' => $user['id']], $fields);
    }
    
    /**
     * Get all users
     */
    public function getAll($order = '') {
        return $this->db->selectAllRows($this->subClass, $this->tableName, '*', $order);
    }

}
