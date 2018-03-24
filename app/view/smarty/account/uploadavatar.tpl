<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/account/user", 'Account'), 'Change profile photo')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('Change profile photo', 'id.png')}
        </div>
    </div>
</div>

<div class="ui three item menu">
  <a href="/account/settings" class="item">Settings</a>
  <a href="/account/password" class="item">Change Password</a>
  <a href="/account/avatar" class="active item">Upload Avatar</a>
</div>

<p class="ui message info">
Click the browse button to locate a file to use as your profile picture. Rude and offensive pictures may be deleted.
<br><br><b>.jpg</b> images only please!
</p>

<p></p>

<form action='/account/changeprofilephoto' method='POST' enctype='multipart/form-data' class="ui form">
    <div class="field">
        <input type="file" name="avatar" id="file">
    </div>
    <div style="text-align:right; width:100%;">
        <input type='submit' name='fld_submit_uploadphoto' value='Change Avatar' class="ui button teal" />
    </div>
</form>