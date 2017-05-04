{viewCrumbtrail(array("/account", 'Account'), 'Change profile photo')}
{viewPageHeader('Change profile photo', 'id.png')}

<p>Click the browse button to locate a file to use as your profile picture. Rude and offensive pictures may be deleted.</p>

<p><b>.jpg</b> images only please!</p>

<form action='/account/changeprofilephoto' method='POST' enctype='multipart/form-data'>
    <label for="fld_avatar">Filename:</label>
    <input type='file' name='avatar' id='file' />
    
    <div style="text-align:right; width:100%;">
        <input type='submit' name='fld_submit_uploadphoto' value='Change Avatar' class="button_blue" />
    </div>
</form>