<!--
This will be the view to a new type of post - youtube where the user can enter a youtube video ID as well as a title and text - the video will be shown large on the blog

-->
<?php
function showVideoPostForm($arrayBlog, $arrayPost=null) {
        
    if(strtolower(getType($arrayPost)) !== 'null') {
        // We are editing the post
        $formAction = CLIENT_ROOT_BLOGCMS.'/posts/'.$arrayBlog['id'].'/edit/'.$arrayPost['id'].'/submit';
        $fieldTitle = $arrayPost['title'];
        $fieldContent = $arrayPost['content'];
        $fieldTags = str_replace("+"," ",$arrayPost['tags']);
        $fieldVideoID = $arrayPost['videoid'];
        $submitLabel = 'Update';
        $mode = 'edit';
        $postdate_d = substr($arrayPost['timestamp'], 8, 2);
        $postdate_m = substr($arrayPost['timestamp'], 5, 2);
        $postdate_y = substr($arrayPost['timestamp'], 0, 4);
        $postdate_h = substr($arrayPost['timestamp'], 11, 2);
        $postdate_i = substr($arrayPost['timestamp'], 14, 2);
        
    } else {
        // This must be a new post
        $formAction = CLIENT_ROOT_BLOGCMS.'/posts/'.$arrayBlog['id'].'/new/submit';
        $fieldTitle = '';
        $fieldContent = '';
        $fieldTags = '';
        $fieldVideoID = '';
        $submitLabel = 'Create';
        $mode = 'create';
        $postdate_d = date('d');
        $postdate_m = date('m');
        $postdate_y = date('Y');
        $postdate_h = date('H');
        $postdate_i = date('i');
    }
    
    
/***********************************************************************************
    HTML
***********************************************************************************/
?>

	<script type="text/javascript">
	// this is now out of date as we have the rtf editor
	// handy little script however...
	function displayHTML() {
		var inf = document.upload_data.fld_postcontent.value;
		win = window.open(", ", 'popup', 'toolbar = no, status = no, width = 600, height = 400');
		win.document.write("" + inf + "");
	}
	</script>    
	
	<div class="crumbtrail"><a href="<?=CLIENT_ROOT_BLOGCMS?>">Home</a><a href='<?=CLIENT_ROOT_BLOGCMS?>/overview/<?=$arrayBlog['id'] ?>'><?=$arrayBlog['name']?></a><a>New Video Post</a></div>
	
	<img src="<?=CLIENT_ROOT?>/resources/icons/64/add_doc.png" class="settings-icon" /><h1 class="settings-title" style="margin-top:0px;">Create New Blog Post <span style="color:blue;">+Video</span><br><span class="subtitle"><?=$arrayBlog['name']?></span></h1>
	
	<form action="<?=$formAction?>" method="post" name="frm_createpost" id="frm_createpost">
	
        <div class="editpost-centre" style="width:70%; display:inline-block; vertical-align:top;">
        
		<label for="fld_posttitle">Title</label>
		<input type="text" name="fld_posttitle" id="fld_posttitle" size="50" autocomplete="off" value="<?=$fieldTitle?>" />
        
        <label for="fld_postvideosource">Video Source</label>
        <select name="fld_postvideosource">
            <option value="youtube">YouTube</option>
            <option value="vimeo">Vimeo</option>
        </select>
        
        <label for="fld_postvideoID">Video ID <a href="#" onclick="alert('Youtube ID are found in hte URL youtube.com/user/?v={URL}'); return false;">[?]</a></label>
		<input type="text" name="fld_postvideoID" placeholder="Enter a YouTube or Vimeo Video ID" id="fld_postvideoID" size="50" autocomplete="off" value="<?=$fieldVideoID?>" />
		
		<label for="fld_postcontent">Content</label>
        <button type="button" onclick="rbrtf_showWindow('<?=CLIENT_ROOT_BLOGCMS?>/ajax/image_upload.php?blogid=<?=$arrayBlog['id']?>')" title="Insert Image"><img src="<?=CLIENT_ROOT_RESOURCES?>/icons/document_image_add_32.png" style="width:15px; height:15px;" /></button>
        <p style="font-size:80%;">Note - <a href="https://daringfireball.net/projects/markdown/syntax" target="_blank">Markdown</a> is supported!</p>
        <textarea name="fld_postcontent" id="fld_postcontent" style="height:30vh;"><?=$fieldContent?></textarea>
        
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
		<input type="text" name="fld_tags" id="fld_tags" placeholder="Enter as a Comma Seperated List" autocomplete="off" value="<?=$fieldTags?>" />
		
        <div class="push-right">
            <?php if($mode == 'edit'): ?>
            <input type="hidden" name="fld_postid" value="<?=$arrayPost['id']?>" />
            <?php endif; ?>
			<input type="hidden" name="fld_blogid" value="<?=$arrayBlog['id']?>" />
            <input type="hidden" name="fld_posttype" value="video" />
			<input type="submit" name="fld_submitpost" value="<?=$submitLabel?>" />
            <input type="button" value="Cancel" name="goback" onclick="window.history.back()" />
		</div>
            
        </div><div class="editpost-options" style="width:30%; display:inline-block; vertical-align:top;">
        
		<label for="fld_postdate">Schedule Post<br><span style="font-weight:normal; font-style:italic;">Set custom date and time to show the post on your blog.</span></label>
		<input type="text" name="fld_postdate_d" style="width:26px;" value="<?=$postdate_d?>" /> /
		<input type="text" name="fld_postdate_m" style="width:26px;" value="<?=$postdate_m?>" /> /
		<input type="text" name="fld_postdate_y" style="width:40px;" value="<?=$postdate_y?>" />
        &nbsp;&nbsp;
		<input type="text" name="fld_postdate_h" style="width:26px;" value="<?=$postdate_h?>" /> : 
		<input type="text" name="fld_postdate_i" style="width:26px;" value="<?=$postdate_i?>" />
		
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
			var blog_id = <?=$arrayBlog['id']?>;
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
        
        <?php if($mode == 'edit'): ?>
        // Apply Defaults
        $("#fld_draft").val(<?=$arrayPost['draft']?>);
        $("#fld_allowcomment").val(<?=$arrayPost['allowcomments']?>);
        $("#fld_videosource").val(<?=$arrayPost['videosource']?>);
        <?php endif; ?>
        
	});
	</script>
	
<?php } ?>