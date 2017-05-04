<img src="/resources/icons/64/add_doc.png" class="settings-icon" /><h1 class="settings-title">Create a new blog</h1>

<form action="/submitnewblog" method="post" onsubmit="return checkForm(this);">

	<input type="text" name="fld_generic" id="fld_generic" class="nobots" />

	<label for="fld_blogname">Blog Name</label>
	<input type="text" name="fld_blogname" id="fld_blogname" size="50" autocomplete="off" data-notValidText="Please enter a name for your new blog, this must be at least 6 characters!" onkeyup="validate(this, {ldelim}fieldlength:6{rdelim})" />
	
	<label for="fld_blogdesc">Description</label>
	<textarea name="fld_blogdesc" id="fld_blogdesc" rows="15" cols="53"></textarea>

	<div class="push-right">
		<input type="hidden" name="secure_form_key" value="{$securekey}" />
		<input type="submit" name="submit_blog" value="Submit" />
	</div>
</form>