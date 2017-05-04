<?php
/**
 NO LONGER USED - See 'standard post' - does both new and edit
**/


function showAddPostForm($pobjBlog) { ?>

	<script type="text/javascript">
	// this is now out of date as we have the rtf editor
	// handy little script however...
	function displayHTML() {
		var inf = document.upload_data.fld_postcontent.value;
		win = window.open(", ", 'popup', 'toolbar = no, status = no, width = 600, height = 400');
		win.document.write("" + inf + "");
	}
	</script>    
	
	<div class="crumbtrail"><a href="<?=CLIENT_ROOT_BLOGCMS?>">Home</a><a href='<?=CLIENT_ROOT_BLOGCMS?>/overview/<?=$pobjBlog['id'] ?>'><?=$pobjBlog['name']?></a><a>New Post</a></div>
	
	<img src="<?=CLIENT_ROOT?>/resources/icons/64/add_doc.png" class="settings-icon" /><h1 class="settings-title" style="margin-top:0px;">Create New Blog Post<br><span class="subtitle"><?=$pobjBlog['name']?></span></h1>
	
	<form action="<?=CLIENT_ROOT_BLOGCMS?>/posts/<?=$pobjBlog['id']?>/new/submit" method="post" name="frm_createpost" id="frm_createpost">
	
		<label for="fld_posttitle">Title</label>
		<!-- <a href="" onclick="javascript:alert('Alpha-numberic characters only! Anything that is not a letter or a number will be stripped from the title!');return false;">[?]</a>-->
		<input type="text" name="fld_posttitle" id="fld_posttitle" size="50" autocomplete="off" />
		
        <!-- NON Fancy Editor -->
		<label for="fld_postcontent">Content</label>
        
        <button type="button" onclick="rbrtf_showWindow('<?=CLIENT_ROOT_BLOGCMS?>/ajax/image_upload?blogid=<?=$pobjBlog['id']?>')" title="Insert Image"><img src="<?=CLIENT_ROOT_RESOURCES?>/icons/document_image_add_32.png" style="width:15px; height:15px;" /></button>
        
        <p style="font-size:80%;">Note - <a href="https://daringfireball.net/projects/markdown/syntax" target="_blank">Markdown</a> is supported!</p>
        <textarea name="fld_postcontent" id="fld_postcontent"></textarea>
        
    <!--
        <div id="postcontent"></div>
        
		<script type="text/javascript">
		$("#postcontent").rbrtf({
			"urlroot": "../../../..",
			"rawvalueid": "fld_postcontent",
			"customControls": "  <button onclick=\"rbrtf_showWindow('../../ajax/image_upload.php?blogid=1060314297')\" title=\"Insert Image\"><img src=\"../../../../resources/icons/document_image_add_32.png\" style=\"width:15px; height:15px;\" /></button>"
		});
		</script>
		
		Wiki is not live!
		<div class="push-right" style="font-size:12px;">
			<a href="<?=CLIENT_ROOT?>/wiki/index.php?a=34" class="wikihelp" target="_blank">About RBWiki Markup</a>
		</div>-->
		
		<label for="fld_tags">Tags</label>
		<input type="text" name="fld_tags" id="fld_tags" placeholder="Enter as a Comma Seperated List" autocomplete="off" />
		
		<!--
		<h3>Post Options</h3>
		<label for="fld_postdate">Schedule Post<br><span style="font-weight:normal; font-style:italic;">Set custom date and time to show the post</span></label>
		<input type="text" name="fld_postdate" style="width:26px;" value="<?=date("d")?>" /> /
		<input type="text" name="fld_postdate" style="width:26px;" value="<?=date("m")?>" /> /
		<input type="text" name="fld_postdate" style="width:40px;" value="<?=date("Y")?>" />
		-->
		
		<label for="fld_allowcomment">Allow Comments <a href="/" onclick="alert('This option will allow logged in users to post comments on your blog posts. You can control whether these are shown automatically in the blog settings.'); return false;">[?]</a></label>
		<select name="fld_allowcomment" id="fld_allowcomment">
			<option value="1">Yes</option>
			<option value="0">No</option>
		</select>
		
		
		<label for="fld_draft">Action to take <a href="/" onclick="alert('Post to Blog: This post will be live on your blog for anyone that can see you blog to read. Blog security settings can be changed in the settings section.\n\nSave as Draft: The post will be saved for further editing later and will not be visible to your readers.'); return false;">[?]</a></label>
		<select name="fld_draft" id="fld_draft">
			<option value="0">Post to Blog</option>
			<option value="1">Save as Draft</option>
		</select>
		
		<div class="push-right">
			<input type="hidden" name="fld_blogid" value="<?=$pobjBlog['id']?>" />
			<input type="submit" name="fld_submitpost" value="Save" />
		</div>
	</form>
	
	
	<script type="text/javascript">		
	$(document).ready(function () {
	
		// Function to submit form
		var submitForm = function () {
			document.frm_createpost.submit();
		};
		
		// Handle form submission
		$("#frm_createpost").submit(function() {
		
			// Check that the post title is unique
			var post_title = $("#fld_posttitle").val();
			var blog_id = <?=$pobjBlog['id']?>;
			var url = "<?=CLIENT_ROOT_BLOGCMS?>/ajax/ajax_checkDuplicateTitle.php?blog_id=" + blog_id + "&post_title=" + post_title;
			
			$.get(url, function(data) {
				if(data !== "false") alert("Validation Failed - Title needs to be unique for this blog!");
				return false;
			});
			
			// Update the wiki code
			if($(".rbrtf-rtfinput").is(":visible")) {
				// Update wiki fields before submitting
				var htmlcontent = $(".rbrtf-editor .rbrtf-rtfinput").html();
				jQuery.get("<?=CLIENT_ROOT?>/core/ajax/ajax_viewWikiMarkup.php", {content:htmlcontent},  function(data) {
					$(".rbrtf-editor .rbrtf-wikiinput").val(data);
					submitForm();
				});
				// Wait for the ajax to submit the form on completion
				return false;
			}
			return true;
		});
	});
	</script>
	
<?php } ?>