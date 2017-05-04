<?php
namespace rbwebdesigns;
// notes - logins - should we add a session id so that only one session can exist at once
// only when this function is called will it be reset?
// - auto logout?
// session manager class

class AccountManager {

	private $db;

    public function __construct($dbConnection) {
		$this->db = $dbConnection;
        $this->modelUsers = $GLOBALS['modelUsers'];
    }
	
	/**
		Note - this function assumes parameters have been sanitized!
		Should be safe as only accessible from this class
	**/
	private function findUser($strUsername, $strPassword) {
		$arrWhere = array(
			'username' => $strUsername,
			'password' => $strPassword
		);
		return $this->db->selectSingleRow(TBL_USERS, array('id','admin'), $arrWhere);
	}

    public function login($strUsername, $strPassword) {
        
        // User Input
        $strUsername = safeString($strUsername);
        $strPassword = md5(safeString($strPassword));
		
        $arrUser = $this->findUser($strUsername, $strPassword);
		
		if(gettype($arrUser) == 'array') {
			
			// Set the user that is logged in
			$_SESSION['userid'] = $arrUser['id'];
			$_SESSION['admin'] = $arrUser['admin'];
			
			return true;
		}
		else return false;
    }
	
	public function logout() {
	
	}

	public function changePassword() {
		// put here?
	}
    
    public function newAccount()
    {
        // Check a username and password were provided
        if(strlen($_POST['fld_password']) < 8) return false;
        if(strlen($_POST['fld_username']) < 2) return false;
        
        // Check passwords match
        if(sanitize_string($_POST['fld_password']) != sanitize_string($_POST['fld_password_2']))
        {
            return false;
        }
        
        // Check if this username already exists
        if($this->modelUsers->getCount(array('username' => sanitize_string($_POST['fld_username']))) > 0)
        {
            return false;
        }
        
        return $this->modelUsers->insert(array(
            'name'     => sanitize_string($_POST['fld_name']),
            'surname'  => sanitize_string($_POST['fld_surname']),
            'username' => sanitize_string($_POST['fld_username']),
            'password' => md5(sanitize_string($_POST['fld_password'])),
            'email'    => sanitize_string($_POST['fld_email']),
            'admin'    => 0,
            'signup_date' => date('Y-m-d H:i:s')
        ));
    }
}
?>