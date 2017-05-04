{viewCrumbtrail(array("/overview/{$blog.id}", {$blog.name}, "/config/{$blog.id}", 'Settings'), 'General Settings')}
{viewPageHeader('General Settings', 'id.png', {$blog.name})}

<form action="/config/{$blog.id}/general/submit" method="POST">
    
	<label for="fld_blogname">Blog Name</label>
	<input type="text" value="{$blog.name}" name="fld_blogname" />
	
	<label for="fld_blogdesc">Description</label>
	<textarea name="fld_blogdesc">{$blog.description}</textarea>
	
	<label for="fld_blogsecurity">Who should be able to read your blog?</label>
	<select id="fld_blogsecurity" name="fld_blogsecurity">
		<option value="anon">Everyone</option>
		<option value="members">Logged In Members</option>
		<option value="friends">Your Friends</option>
		<option value="private">Private (Just You)</option>
	</select>
    
	<!--Set Default-->
	<script type="text/javascript">$("#fld_blogsecurity").val("{$blog.visibility}");</script>
	
	<div class="push-right">
        <input type="button" value="Cancel" name="goback" onclick="window.history.back()" />
	    <input type="submit" value="Update" />
	</div>
</form>