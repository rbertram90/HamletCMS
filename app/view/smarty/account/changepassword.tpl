<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/account/user", 'Account'), 'Change Password')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('Change Password', 'lock.png')}
        </div>
    </div>
</div>

<div class="ui three item menu">
  <a href="/account/settings" class="item">Settings</a>
  <a href="/account/password" class="active item">Change Password</a>
  <a href="/account/avatar" class="item">Upload Avatar</a>
</div>


<form action="/account/password" method="POST" class="ui form">
    <div class="field">
        <label for="fld_password">Current Password</label>
        <input type="password" name="fld_current_password" onkeyup="validate(this,{ldelim}password:true{rdelim})" />            
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