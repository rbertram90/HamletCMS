{viewCrumbtrail(array("/overview/{$blog['id']}", $blog['name'], "/config/{$blog['id']}", 'Settings'), 'Edit Stylesheet')}
{viewPageHeader('Edit Stylesheet', 'css.png', $blog['name'])}

<form action="/config/{$blog.id}/stylesheet/submit" method="POST">
    
	<label for="fld_css">CSS<br/>
    
	<p class="info"><i style="font-weight:normal;">Please beware this feature is targeted at
        advanced users, if you don't want to completely ruin your blog I suggest using the
        <a href="/config/{$blog.id}/blogdesigner">blog designer</a>!</i>
    </p></label>
    
	<textarea name="fld_css" id="fld_css" style="height:500px; font-family:monospace;">{strip}
		{file_get_contents("{$serverroot}/app/www_root/blogdata/{$blog['id']}/default.css")}
	{/strip}</textarea>
	
	<div class="push-right">
        <input type="button" value="Cancel" name="goback" onclick="window.history.back()" />
		<input type="submit" name="submit_update" value="Save" />
	</div>
</form>