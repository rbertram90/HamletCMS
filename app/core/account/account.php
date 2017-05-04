<?php
/******************************************************************************
    manage_account.php
    @description set-up code for the account management page (view stored
    seperately).
    @author R Bertram
    @date 12 JAN 2013
******************************************************************************/
    
    // General Account Changes Made
    if(isset($_POST['fld_submit_accchange'])):
        
        $details = array(
            "name" => sanitize_string($_POST['fld_firstname']),
            "surname" => sanitize_string($_POST['fld_surname']),
            "description" => sanitize_string($_POST['fld_description']),
            "email" => sanitize_email($_POST['fld_email']),
            "gender" => sanitize_string($_POST['fld_gender']),
            "location" => sanitize_string($_POST['fld_location']),
            "username" => sanitize_string($_POST['fld_username'])
        );
        
        // Sanitize Date Input
        $in_dob_day = sanitize_number($_POST['fld_dob_day']);
        $in_dob_month = sanitize_number($_POST['fld_dob_month']);
        $in_dob_year = sanitize_number($_POST['fld_dob_year']);
        
        // Check the date combination actually exists!
        if(checkdate($in_dob_month, $in_dob_day, $in_dob_year)) {
            // Convert to date
            $details['dob'] = date("Y-m-d", strtotime($in_dob_year."-".$in_dob_month."-".$in_dob_day));
        
        } else {
            echo showError("Date of birth entered was not a real date, remains unchanged!");
        }
                        
        // Check that if the username has changed then if this one is avaliable
        $this->mdlUsers->updateDetails($details);
        echo showInfo("Profile Updated!");
        
    endif;
    
    if(isset($_POST['fld_submit_passwordchange'])):
    
        // Change Password
        $details = array(
            "current_password" => safeString($_POST['fld_current_password']),
            "new_password" => safeString($_POST['fld_new_password']),
            "new_password_rpt" => safeString($_POST['fld_new_password_rpt'])
        );
        
        // Update DB
        $lbupdate = $this->mdlUsers->updatePassword($details);
        if($lbupdate) echo showInfo("Password Updated!");
        
    endif;
    
    // $task = isset($_GET['t']) ? sanitize_string($_GET['t']) : 'u';
?>

<div class="form-wrapper" style="width:60%;">

<nav id="account_navigation">
    <a href="<?=CLIENT_ROOT?>/account/editprofile" <?=($task=='editprofile') ? 'class="current"':''?>>Update Profile</a> | 
    <a href="<?=CLIENT_ROOT?>/account/changepassword" <?=($task=='changepassword')? 'class="current"':''?>>Change Password</a> |
    <a href="<?=CLIENT_ROOT?>/account/changeavatar" <?=($task=='changeavatar') ? 'class="current"':''?>>Upload New Avatar</a>
</nav>

<?php
    switch($task) {
      case 'changepassword':
        echo '<h2>Change Password</h2>';
        // Include the HTML form for the main person details
        include_once SERVER_PATH_CORE.'/account/change_password.php';
        break;

      case 'changeavatar':
        echo '<h2>Change Profile Photo</h2>';
        // Include the HTML form for the main person details
        include_once SERVER_PATH_CORE.'/account/change_avatar.php';
        break;

      default:
        // get user info from the database
        $user = $this->mdlUsers->getUserById($_SESSION['userid']); 
        echo '<h2>Manage Account</h2>';
        // Include the HTML form for the main person details
        include_once SERVER_PATH_CORE.'/account/edit_account.php';
        break;
    }
?>
</div>