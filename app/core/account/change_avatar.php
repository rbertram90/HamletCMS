<?php
/******************************************************************************
    change_avatar.php
    @description front-end form to change the users profile picture.
    @author R Bertram
    @date 12 JAN 2013
******************************************************************************/
?>

<p>Click the browse button to locate a file to use as your profile picture. Rude and offensive pictures may be deleted.</p>

<p><b>.jpg</b> images only please!</p>

<form action='index.php?page=account' method='POST' enctype='multipart/form-data'>
    <label for="fld_avatar">Filename:</label>
    <input type='file' name='fld_avatar' id='file' />
    
    <div style="text-align:right; width:100%;">
        <input type='submit' name='fld_submit_avatar' value='Change Avatar' class="button_blue" />
    </div>
</form>