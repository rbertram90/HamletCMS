<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/overview/{$blog['id']}", $blog['name'], "/config/{$blog['id']}", 'Settings'), 'Template Gallery')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('Template Gallery', 'star_doc.png', $blog['name'])}
        </div>
    </div>
</div>


<div class="ui segment secondary"><strong>Notice</strong>: Applying a new Template will overwrite any changes you have made using the blog designer!</div>

<style>
    .template_wrapper { border:1px solid #ddd; float:left; width:49%; padding:10px; border-radius:4px; box-sizing:border-box; background-color:#fff; margin-right:2%; margin-bottom:2%; }
    .template_wrapper:nth-child(even) { margin-right:0; }
    .template_wrapper img { width:100%; }
</style>

<div class="template_wrapper">
	<h3>Default Blue</h3>
	<img src="/images/template_screenshots/defaultblue.png" alt="Default Blue Template" width="300" />
	<p>Classic blog design</p>
	<form action="/config/{$blog.id}/template/submit" method="post">
		<input type="hidden" value="tmplt_default_blue" name="template_id" />
		<div class="push-right">
			<input type="submit" class="ui button teal" value="Apply to Blog" />
		</div>
	</form>
</div>


<div class="template_wrapper">
	<h3>Default Blue - Menu Aligned Left</h3>
	<img src="/images/template_screenshots/template_menu_align_left.png" alt="Blue Template with menu aligning left" width="300" />
	<p>Classic blog design with a slight tweak</p>
	<form action="/config/{$blog.id}/template/submit" method="post">
		<input type="hidden" value="tmplt_blue_rmenu" name="template_id" />
		<div class="push-right">
			<input type="submit" class="ui button teal" value="Apply to Blog" />
		</div>
	</form>
</div>


<div class="template_wrapper">
	<h3>Black and Yellow</h3>
	<img src="/images/template_screenshots/black_and_yellow.png" alt="Black Template with yellow sub-colour" width="300" />
	<p>A night time feel blog template with hints of construction about it</p>
	<form action="/config/{$blog.id}/template/submit" method="post">
		<input type="hidden" value="tmplt_black_yellow" name="template_id" />
		<div class="push-right">
			<input type="submit" class="ui button teal" value="Apply to Blog" />
		</div>
	</form>
</div>


<div class="template_wrapper">
	<h3>Skate</h3>
	<img src="/images/template_screenshots/skate.png" alt="Screenshot of Skate Template" width="300" />
	<p>A black and white theme, inspired by skate culture</p>
	<form action="/config/{$blog.id}/template/submit" method="post">
		<input type="hidden" value="tmplt_skate" name="template_id" />
		<div class="push-right">
			<input type="submit" class="ui button teal" value="Apply to Blog" />
		</div>
	</form>
</div>

    <input type="button" value="Cancel" class="ui button" name="goback" onclick="window.history.back()" />