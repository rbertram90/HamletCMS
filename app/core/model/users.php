<?php
/**************************************************************************
     class-users.php
     @description provides access the users database
     @author R.Bertram
     @date 2013 - Rewritten Jan 2013 to incorporate PDO
***************************************************************************/

namespace rbwebdesigns;

class Users extends RBModel
{
	protected $db, $dbc, $tblname, $fields;
	
	// Class Constructor
	public function __construct($databaseConnection)
    {
		$this->db = $databaseConnection;
        $this->tblname = TBL_USERS;
        $this->fields = array(
            'id' => 'number',
            'name' => 'string',
            'surname' => 'string',
            'username' => 'string',
            'password' => 'string',
            'email' => 'string',
            'dob' => 'datetime',
            'gender' => 'string',
            'location' => 'string',
            'profile_picture' => 'string',
            'description' => 'memo',
            'admin' => 'boolean',
            'signup_date' => 'datetime',
            'flickrid' => 'string',
            'security_q' => 'string',
            'security_a' => 'string'
        );
	}

	// Get user by username
	public function getUserByUsername($strUsername)
    {
		return $this->db->selectSingleRow(TBL_USERS, '*', array(
            'username' => safeString($strUsername)
        ));
	}
	
	// Get user by id
	public function getUserById($intUserId)
    {
		return $this->db->selectSingleRow(TBL_USERS, '*', array(
            'id' => safeNumber($intUserId)
        ));
	}
	
	// Get user by email address
	public function getUserByEmail($strEmail)
    {
		return $this->db->selectSingleRow(TBL_USERS, '*', array(
            'email' => safeString($strEmail)
        ));
	}
    
    public function findUserByUsername($username)
    {
        return $this->db->selectMultipleRows(TBL_USERS, '*', 'username LIKE "%' . $username . '%"');
    }
	
	// Get all memebers
	public function getAll()
    {
        $arrWhat = array('id', 'username', 'signup_date', 'profile_picture');
        return $this->db->selectMultipleRows(TBL_USERS, $arrWhat, $arrWhere);
	}
	
	// Delete a user
	public function deleteAccount($id)
    {
		try
        {
			// Query Database
			$this->db->query("DELETE FROM ".TBL_USERS." WHERE id=".$id, $this->db);
			
		} catch(PDOException $e) { die(showQueryError($e)); }
        
		// Logout
		session_destroy();
		
		// Redirect to homepage
		header("Location: index.php");
	}
	
	// Update the details of an account
	public function updateDetails($fields)
    {
		// Loop through all values in the array fields adding them to the SQL WHERE string
		$sql_set_clause = "";
		foreach($fields as $key=> $value)
        {
			$sql_set_clause .= " $key='$value',";
		}
		
		// Remove the final unwanted comma
		$sql_set_clause = substr($sql_set_clause,0,-1);
		
		try {
			$this->db->runQuery("UPDATE ".TBL_USERS." SET $sql_set_clause WHERE id='".$_SESSION['userid']."'");
			
		} catch(PDOException $e) { die(showQueryError($e)); }
		        
		return true;
	}

	// Stage 1 for password update
	public function updatePassword($details)
    {
		// Check the repeated passwords match
		if($details['new_password'] != $details['new_password_rpt'])
        {
			echo showError("Password do not match!");
			return;
		}
		
		$lobjUser = $this->getUserById($_SESSION['userid']);
		
		// Check the given password matches the database
		if(md5($details['current_password']) != $lobjUser['password'])
        {
			echo showError("Existing password didn't match!");
			return;
		}
		
		$md5password = md5($details['new_password']);
		
		try
        {
			$this->dbc->query("UPDATE ".TBL_USERS." SET password='$md5password' WHERE id='".$_SESSION['userid']."'");
			
		} catch(PDOException $e) { die(showQueryError($e)); }
		
		return true;
	}
	
	// Password reset request submitted
	public function resetPassword($user)
    {
		// Generate a password
		$newpswd = generateRandomAlphaNumeric(8);
		$hashpswd = md5($newpswd);
		
		// Update database
		try {
			 $this->dbc->query("UPDATE ".TBL_USERS." SET password='$hashpswd' WHERE id='$user'");
			 
		} catch(PDOException $e) { die(showQueryError($e)); }
		
		// Return the new password
		return $newpswd;
	}
	
	// Get the last $num most recent members
	public function getRecent($num)
    {
		try
        {
			$query_recent = $this->dbc->query("SELECT id,name,profile_picture FROM ".TBL_USERS." ORDER BY signup_date DESC LIMIT ".$num);
			
		} catch(PDOException $e) { die(showQueryError($e)); }
		
		return $query_recent->fetchAll(PDO::FETCH_ASSOC);
	}

/*
	// Get an array of friends data
	public function getFriendsList($id)
    {
		$query_string = "SELECT * FROM rbwebdesigns.friends WHERE userid='$id' OR friendid='$id'";
		return $this->db->select_multi($query_string);
	}
	
	// Get an array of friends data full of data from user table
	public function expandedFriendsList($id)
    {
		$larrFriends = $this->getFriendsList($id);
		$res = array();
				
		foreach($larrFriends as $friendid):
		
			if($friendid['friendid'] == $id) {
				$res[] = $this->getUserById($friendid['userid']);
			} else {
				$res[] = $this->getUserById($friendid['friendid']);
			}

		endforeach;
		
		return $res;
	}
*/
}
?>