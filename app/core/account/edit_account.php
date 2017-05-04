<?php
/******************************************************************************
    change_avatar.php
    @description front-end form to change the users details stored
    on the system.
    @author R Bertram
    @date 12 JAN 2013
******************************************************************************/

// $user is set in account.php and is an array of all user data
?>

<form action="<?=CLIENT_ROOT?>/account/details/submit" method="POST">

    <label for="fld_firstname">First Name</label>
    <input type="text" name="fld_firstname" value="<?=$user['name']?>" onkeyup="validate(this,{fieldlength:2})" />
    
    <label for="fld_surname">Surname</label>
    <input type="text" name="fld_surname" value="<?=$user['surname']?>" onkeyup="validate(this,{fieldlength:2})" />
    
    <label for="fld_description">Description</label>
    <textarea name="fld_description"><?=$user['description']?></textarea>
    
<?php
    $dob = getdate(strtotime($user['dob']));
    $year = $dob['year'];
    $month = $dob['mon'];
    $day = $dob['mday'];

    echo "<label for='fld_dob_day'>Date of Birth</label><select name='fld_dob_day'>";
	
    for($i=1; $i<32; $i++) {
        echo "<option value='$i'";
        if ($day == $i) echo "selected";
        echo ">$i</option>";
    }
    
    echo "</select> / <select name='fld_dob_month'>";
	
    for($j=1; $j<13; $j++) {
        echo "<option value='$j'";
        if ($month == $j) echo "selected";
        echo ">$j</option>";
    }

    echo "</select> / <select name='fld_dob_year'>";
	
    $thisyear = date("Y");

    for($k = 1899; $k<=$thisyear; $k++) {
        echo "<option value='$k'";
        if ($year == $k) echo "selected";
        echo ">$k</option>";
    }

    echo "</select>";

?>
    
    <label for="fld_gender">Gender</label>
    <select name="fld_gender">
        <option <?php echo $user['gender'] == "Male" ? "selected" : ""; ?>>Male</option>
        <option <?php echo $user['gender'] == "Female" ? "selected": ""; ?>>Female</option>
    </select>
    
    <label for="fld_location">Location</label>
    <input type="text" name="fld_location" value="<?php echo $user['location']; ?>" />
     
    <label for="fld_username">Username</label>
    <input type="text" name="fld_username" value="<?php echo $user['username']; ?>" />
    
    <label for="fld_email">Email</label>
	<input type="text" name="fld_email" onkeyup="validate(this,{email:true})" value="<?php echo $user['email']; ?>" />
    
	<div style="text-align:right; width:100%;">
        <input type="submit" name="fld_submit_accchange" value="Update Account" class="button_blue" />
    </div>
</form>