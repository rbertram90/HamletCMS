<?php
class Friends {
    private $db;
	
	// Construct
	public function __construct($conn) {
        // Connection to sample3 database
        $this->db = $conn->getConnection();
		
		$this->tblname = 'rbwebdesigns.friends';
        $this->tblusers = 'rbwebdesigns.users';
	}
	
    public function isFriend($user,$friend) {
        try {
            $getfriends = $this->db->query("SELECT COUNT(*) FROM ".FRIENDS." WHERE ((userid=$user AND friendid=$friend) OR (userid=$friend AND friendid=$user)) AND confirm=1");
            
            $result = $getfriends->fetch();
            
            if($result[0] > 0) {
                return true;
            } else {
                return false;
            }
        } catch(PDOException $e) { die(showQueryError($e)); }
    }
    
	// Get an array of friends for a specified user
	public function getFriends($id, $num = 10) {
        try {
            $sql = "SELECT {$this->tblusers}.* ";
            $sql.= "FROM {$this->tblname} ";
            $sql.= "LEFT JOIN {$this->tblusers} ON {$this->tblname}.userid = {$this->tblusers}.id ";
            $sql.= "WHERE userid={$id} ";
            $sql.= "AND confirm=1 ";
            $sql.= "LIMIT {$num}";
            
            $getfriends = $this->db->query($sql);
            $result1 = $getfriends->fetchAll(PDO::FETCH_ASSOC);
            
            $sql = "SELECT {$this->tblusers}.* ";
            $sql.= "FROM {$this->tblname} ";
            $sql.= "LEFT JOIN {$this->tblusers} ON {$this->tblname}.userid = {$this->tblusers}.id ";
            $sql.= "WHERE friendid={$id} ";
            $sql.= "AND confirm=1 ";
            $sql.= "LIMIT {$num}";
            
            $getfriends2 = $this->db->query($sql);
            $result2 = $getfriends2->fetchAll(PDO::FETCH_ASSOC);
            
        } catch(PDOException $e) { die(showQueryError($e)); }
        
		return array_merge($result1, $result2);
	}
	
	// Get the people who have requested your friendship
	public function getFriendRequests($id) {
        try {
            $getrequest = $this->db->query("SELECT * FROM ".$this->tblname." WHERE friendid=$id AND confirm=0");
            
        } catch(PDOException $e) { die(showQueryError($e)); }
        
		return $getrequest->fetchAll(PDO::FETCH_ASSOC);
	}
	
	public function countFriendRequests($id) {
        try {
			$qs = "SELECT count(*) as friendcount FROM ".$this->tblname." WHERE friendid=$id AND confirm=0";
		
            $getrequest = $this->db->query($qs);
						
			$results = $getrequest->fetch(PDO::FETCH_ASSOC);
						
			if(array_key_exists('friendcount', $results)) {
				return($results['friendcount']);
			} else {
				return 0;
			}
        }
		catch(PDOException $e) { die(showQueryError($e)); }
	}
	
	// Get the users which you have asked to be friends
	public function getInvites($id) {
        try {
            $invites = $this->db->query("SELECT * FROM ".$this->tblname." WHERE userid=$id AND confirm=0");
            
        } catch(PDOException $e) { die(showQueryError($e)); }
        
		return convertToMDArray($invites);
	}
		
	// Add a new friend
	public function addFriend($userid,$friendid) {
        try {
            $check_friend = $this->db->query("SELECT * FROM ".$this->tblname." WHERE (userid='$userid' AND friendid='$friendid') OR (userid='$friendid' AND friendid='$userid')");
            
        } catch(PDOException $e) { die(showQueryError($e)); }
		
        // If friendship doesn't already exist
		if($check_friend->rowCount() == 0) {
            $date = date("Y-m-d");
            try {
                $this->db->query("INSERT INTO ".$this->tblname."(userid,friendid,date) VALUES('$userid','$friendid','$date')");
            } catch(PDOException $e) { die(showQueryError($e)); }
			$status = "Friend Added!";
		}
		else $status = "Friend already exists!";
		return $status;
	}
	
	public function countFriends($userid) {
        try {
            $query = $this->db->query("SELECT count(*) FROM ".$this->tblname." WHERE (userid=$userid OR friendid=$userid) AND confirm=1");
            
			$results = $query->fetch();
			
        } catch(PDOException $e) { die(showQueryError($e)); }
        
		return $results[0];
	}
	
	// Accept a recieved friend invitation
	public function acceptFriend($friendid) {
        try {
            $this->db->query("UPDATE ".$this->tblname." SET confirm='1' WHERE userid='$friendid' AND friendid='".$_SESSION['userid']."'");
            
        } catch(PDOException $e) { die(showQueryError($e)); }
        
		return showInfo("Friend Confirmed!");
	}
	
	// Delete a current friendship
	public function deleteFriend() {
        try {
            $this->db->query("DELETE FROM ".$this->tblname." WHERE ((friendid='".$_POST['friend_id']."' AND userid='".$_SESSION['userid']."') OR (userid='".$_POST['friend_id']."' AND friendid='".$_SESSION['userid']."'))");
            
        } catch(PDOException $e) { die(showQueryError($e)); }
        
		return showInfo("Friend Deleted!");
	}
	
	// Get a friends 'recently' formed friendships
	public function getRecentFriends($friendid) {
        try {
            $getfriends = $this->db->query("SELECT * FROM ".$this->tblname." WHERE (friendid='$friendid' OR userid='$friendid') ORDER by date ASC");
        
        } catch(PDOException $e) { die(showQueryError($e)); }
        
		return $getfriends->fetchAll(PDO::FETCH_ASSOC);
	}
    
    /**
     * getFriendArr() is an inhouse built function which accesses the friends database
     * and returns an array of all the ID of the friends of a specified user
     */
    function getFriendArr($id) {
        try {
            $query_select_friends_1 = $this->db->query("SELECT friendid FROM ".$this->tblname." WHERE (userid='$id' AND confirm=1)");
            
            $query_select_friends_2 = $this->db->query("SELECT userid FROM ".$this->tblname." WHERE (friendid='$id' AND confirm=1)");
        
        } catch(PDOException $e) { die(showQueryError($e)); }
        
        $i = $j = 0;
        $fetch_friends_1 = array();
        $fetch_friends_2 = array();
        while ($row = $query_select_friends_1->fetch()) {
            $fetch_friends_1[$i] = $row['friendid'];
            $i++;
        }
        while ($row = $query_select_friends_2->fetch()) {
            $fetch_friends_2[$j] = $row['userid'];
            $j++;
        }
        
        if ( $query_select_friends_1->rowCount() > 0 ) {
            if ( $query_select_friends_2->rowCount() > 0 ) {
                return array_merge($fetch_friends_1, $fetch_friends_2);
            }
            else return $fetch_friends_1;
        }
        else return $fetch_friends_2;
    }
	
	public function getFriendSuggestions() {
		// Get all the people you have asked to be a friend
		// Need to add friends of people who asked you to be friends
		$suggestions = array();
		$i = 0;
		$gClsUsers = $GLOBALS['gclsUsers'];
		$friends = $this->getFriendArr($_SESSION['userid']);
		   
		// to do: make sure that the suggestion doesn't exists already (might not be confirmed friendship)
		foreach($friends as $key => $friend ):
			
			// Get each of your friends ID numbers
			$friend_id = $friend;
			
			// (In turn) Search for all of the friends of your friends
			$friend_query_2 = $this->getFriends($friend_id, 5);
			
			foreach($friend_query_2 as $friend_2) {
			
				$friend_2_id = $friend_2['friendid'];
				
				// Get the details of the friend of a friend
				if(!in_array($friend_2_id,$friends) && $friend_2_id != $_SESSION['userid']) {
				
					$fetch_details = $gClsUsers->getUserById($friend_2_id);
					
					$suggestions[$i]['image'] = $fetch_details['profile_picture'];
					$suggestions[$i]['name'] = $fetch_details['name']." ".$fetch_details['surname'];
					$suggestions[$i]['id'] = $fetch_details['id'];
					$i++;
				}
			}
		endforeach;
		
		// Return unique suggestions
		return super_unique($suggestions);
	}
}
?>