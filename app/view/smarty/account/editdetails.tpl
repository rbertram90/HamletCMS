{viewCrumbtrail(array("/account", 'Account'), 'Edit')}
{viewPageHeader('Edit Account Details', 'id.png')}

<form action="/account/edit/submit" method="POST">

    <label for="fld_firstname">First Name</label>
    <input type="text" name="fld_firstname" value="{$user.name}" onkeyup="validate(this,{ldelim}fieldlength:2{rdelim})" />
    
    <label for="fld_surname">Surname</label>
    <input type="text" name="fld_surname" value="{$user.surname}" onkeyup="validate(this,{ldelim}fieldlength:2{rdelim})" />
    
    <label for="fld_description">Description</label>
    <textarea name="fld_description">{$user.description}</textarea>
    
    {$dob = getdate(strtotime($user['dob']))}
    
    <label for='fld_dob_day'>Date of Birth</label>
    
    <select name='fld_dob_day'>
    {for $i=1 to 31}
        {if $dob['mday'] = $i}
            <option value='{$i}' selected>{$i}</option>
        {else}
            <option value='{$i}'>{$i}</option>
        {/if}
    {/for}
    </select> / <select name='fld_dob_month'>
	
    {for $j=1 to 12}
        {if $dob['mon'] = $j}
            <option value='{$j}' selected>{$j}</option>
        {else}
            <option value='{$j}'>{$j}</option>
        {/if}
    {/for}
    </select> / <select name='fld_dob_year'>
	
    {for $k=1899 to date("Y")}
        {if $dob['year'] = $j}
            <option value='{$k}' selected>{$k}</option>
        {else}
            <option value='{$k}'>{$k}</option>
        {/if}
    {/for}
    </select>
    
    
    <label for="fld_gender">Gender</label>
    <select name="fld_gender">
        {if $user.gender == 'Male'}
            <option selected>Male</option>
            <option>Female</option>
        {else}
            <option>Male</option>
            <option selected>Female</option>
        {/if}
    </select>
    
    <label for="fld_location">Location</label>
    <input type="text" name="fld_location" value="{$user.location}" />
     
    <label for="fld_username">Username</label>
    <input type="text" name="fld_username" value="{$user.username}" />
    
    <label for="fld_email">Email</label>
	<input type="text" name="fld_email" onkeyup="validate(this,{ldelim}email:true{rdelim}})" value="{$user.email}" />
    
	<div style="text-align:right; width:100%;">
        <input type="submit" name="fld_submit_accchange" value="Update Account" class="button_blue" />
    </div>
</form>