<?php
namespace rbwebdesigns;

class AccountController {
	
    private $db;
    private $mdlUsers;
    
    public function __construct($dbRBWebdesigns) {
        $this->db = $dbRBWebdesigns;
        $this->mdlUsers = new Users($this->db);
    }

    public function routeAccount($DATA, $PARAMS) {
        if(is_array($PARAMS)) {
            $page = sanitize_string($PARAMS[0]);
        } else {
            $page = "";
        }
        
        // Must be logged in...
        if(!isset($_SESSION['userid'])) header('location: /');
        
        switch($page) {
            case "login":
                $DATA = $this->accountLogin($DATA, $PARAMS);
                break;

            case "logout":
                $DATA = $this->accountLogout($DATA, $PARAMS);
                break;

            case "resetpassword":
                $DATA = $this->resetPassword($DATA, $PARAMS); 
                break;

            case "manage":
            case "changepassword":
            case "changeavatar":
            default:
                $DATA = $this->manageAccount($DATA, $PARAMS);
                break;
        }
        return $DATA;
    }

    public function manageAccount($DATA, $PARAMS) {
        $task = sanitize_string($PARAMS[0]);
        require_once SERVER_PATH_CORE.'/account/account.php';
        $DATA['page_title'] = "Manage Account";
        return $DATA;
    }

    public function resetPassword($DATA, $PARAMS) {
        require_once SERVER_PATH_CORE.'/account/lostpassword.php';
        $DATA['page_title'] = "Reset Password";
        return $DATA;
    }

    public function accountLogout($DATA, $PARAMS) {
        
        // Remove the user ID
        unset($_SESSION['userid']);
        
        // Kill the session
        session_destroy();
                
        // Redirect to homepage
        redirect(CLIENT_ROOT.'/');
    }
    
    public function newAccount()
    {
        if(sanitize_string($_POST['fld_password']) != sanitize_string($_POST['fld_password_2']))
        {
            return false;
        }
        
        if($this->mdlUsers->getCount(array('username' => sanitize_string($_POST['fld_username']))) > 0)
        {
            return false;
        }
        
        return $this->mdlUsers->insert(array(
            'name'     => sanitize_string($_POST['fld_name']),
            'surname'  => sanitize_string($_POST['fld_surname']),
            'username' => sanitize_string($_POST['fld_username']),
            'password' => md5(sanitize_string($_POST['fld_password'])),
            'email'    => sanitize_string($_POST['fld_email']),
            'admin'    => 0,
            'signup_date' => date('Y-m-d H:i:s')
        ));
    }

    public function accountLogin($DATA, $PARAMS) {
        if(isset($_POST['fld_username']) && isset($_POST['fld_password'])) {
            
            $account = new AccountManager($this->db);
            
            $boolLogin = $account->login($_POST['fld_username'], $_POST['fld_password']);

            if($boolLogin) {
                $user = $this->mdlUsers->getUserById($_SESSION['userid']);
                $DATA['page_title'] = 'Welcome '.$user['name'];
                echo showSuccess("Login Successful");
            }
            else {
                $DATA['page_title'] = 'Login Failed';
                echo showError("Login Failed");
            }
        } else {
            $DATA['page_title'] = 'Log In';
        }

        if(isset($_SESSION['userid'])) require_once SERVER_ROOT.'/home/accounthome.php';
        else require_once SERVER_ROOT.'/home/mainhome.php';
        
        return $DATA;
    }
}
?>