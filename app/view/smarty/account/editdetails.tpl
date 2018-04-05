<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/cms/account/user", 'Account'), 'Edit')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('Your Information', 'id.png')}
        </div>
    </div>
</div>

<div class="ui three item menu">
  <a href="/cms/account/settings" class="active item">Settings</a>
  <a href="/cms/account/password" class="item">Change Password</a>
  <a href="/cms/account/avatar" class="item">Upload Avatar</a>
</div>

<p class="ui message info">Information provided is not passed on to third parties</p>

<form action="/cms/account/settings" method="POST" class="ui form">

    <div class="two fields">
        <div class="field">
            <label for="fld_firstname">First Name</label>
            <input type="text" name="fld_firstname" value="{$user.name}" onkeyup="validate(this,{ldelim}fieldlength:2{rdelim})">
        </div>
        
        <div class="field">
            <label for="fld_surname">Surname</label>
            <input type="text" name="fld_surname" value="{$user.surname}" onkeyup="validate(this,{ldelim}fieldlength:2{rdelim})">
        </div>
    </div>
    <div class="two fields">
        <div class="field">
            <label for="fld_username">Username</label>
            <input type="text" name="fld_username" value="{$user.username}">
        </div>

        <div class="field">
            <label for="fld_email">Email</label>
            <input type="text" name="fld_email" onkeyup="validate(this,{ldelim}email:true{rdelim}})" value="{$user.email}" />
        </div>
    </div>
    
    <div class="field">
        <label for="fld_description">Description</label>
        <textarea name="fld_description">{$user.description}</textarea>
    </div>
    
    {$dob = getdate(strtotime($user['dob']))}

    <div class="inline fields">
        <label for='fld_dob_day'>Date of Birth</label>
        <div class="field">        
            <select name="fld_dob_day" class="ui dropdown">
            {for $i=1 to 31}
                {if $dob['mday'] == $i}
                    <option value='{$i}' selected>{$i}</option>
                {else}
                    <option value='{$i}'>{$i}</option>
                {/if}
            {/for}
            </select> /
        </div>
        <div class="field">
            <select name="fld_dob_month" class="ui dropdown">
            {for $j=1 to 12}
                {if $dob['mon'] == $j}
                    <option value='{$j}' selected>{"01-`$j`-1980"|date_format:"%B"}</option>
                {else}
                    <option value='{$j}'>{"01-`$j`-1980"|date_format:"%B"}</option>
                {/if}
            {/for}
            </select> /
        </div>
        <div class="field">
            <select name="fld_dob_year" class="ui dropdown">
            {for $k=date("Y") to 1900 step -1}
                {if intval($dob['year']) == $k}
                    <option value='{$k}' selected>{$k}</option>
                {else}
                    <option value='{$k}'>{$k}</option>
                {/if}
            {/for}
            </select>
        </div>
    </div>

    <div class="two fields">
        <div class="field">
            <label for="fld_gender">Gender</label>
            <select name="fld_gender" class="ui dropdown">
                {if $user.gender == 'Male'}
                    <option selected>Male</option>
                    <option>Female</option>
                {else}
                    <option>Male</option>
                    <option selected>Female</option>
                {/if}
            </select>
        </div>

        <div class="field">
            <label for="fld_location">Location</label>
            <input type="text" name="fld_location" value="{$user.location}">
        </div>
    </div>

    <div style="text-align:right; width:100%;">
        <input type="submit" name="fld_submit_accchange" value="Update Account" class="ui teal button" />
    </div>

</form>

<script>
$('select.dropdown').dropdown();
</script>