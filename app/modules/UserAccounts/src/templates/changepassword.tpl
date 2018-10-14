<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/cms/account/user", 'Account'), 'Change Password')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('Change Password', 'lock')}
        </div>
    </div>
</div>

<div class="ui three item menu">
  <a href="/cms/account/settings" class="item">Settings</a>
  <a href="/cms/account/password" class="active item">Change Password</a>
  <a href="/cms/account/avatar" class="item">Upload Avatar</a>
</div>


<form method="POST" class="ui form">
    <div class="field">
        <label for="fld_password">Current Password</label>
        <input type="password" name="fld_current_password">            
    </div>
    <div class="field">
        <label for="fld_new_password">Create New Password</label>
        <input type="password" name="fld_new_password" />
    </div>
    <div class="field">
        <label for="fld_new_password_2">Re-type New Password</label>
        <input type="password" name="fld_new_password_rpt" />
    </div>

    <div style="text-align:right; width:100%;">
        <input type="submit" name="fld_submit_passwordchange" value="Change Password" class="ui button teal">
    </div>
</form>