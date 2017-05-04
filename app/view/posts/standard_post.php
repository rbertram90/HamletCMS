<?php
function showStandardPostForm($arrayBlog, $arrayPost=null) {
        
    if(strtolower(getType($arrayPost)) !== 'null') {
        // We are editing the post
        $formAction = CLIENT_ROOT_BLOGCMS.'/posts/'.$arrayBlog['id'].'/edit/'.$arrayPost['id'].'/submit';
        $fieldTitle = $arrayPost['title'];
        $fieldContent = $arrayPost['content'];
        $fieldTags = str_replace("+"," ",$arrayPost['tags']);
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
    
    // This is also not used... but was an interesting concept
    function openPreview() {
        var previewWin = window.open("<?=CLIENT_ROOT_BLOGCMS?>/blogs/<?=$arrayBlog['id']?>/posts/<?=$arrayPost['link']?>","_blank","height=600,width=800");
        $(previewWin).load(function() {
            alert(typeof previewWin.document);
                  previewWin.document.getElementById('comments').innerHTML = '';
        });
    }
	</script>
    
    <?php
        // Crumbtrail
        echo viewCrumbtrail(array('/overview/'.$arrayBlog['id'], $arrayBlog['name']), 'New Post');
        
        // Title
        echo viewPageHeader($submitLabel.' Blog Post', 'add_doc.png', $arrayBlog['name']);
    ?>
	
	<?php if($mode=='edit' && array_key_exists('autosave', $arrayPost)): ?>
	<script>
		function replaceContent() {
			$("#fld_posttitle").val($("#fld_autosave_title").val());
			$("#fld_postcontent").val($("#fld_autosave_content").val());
			$("#fld_tags").val($("#fld_autosave_tags").val());
			$("#autosave_data").hide();
			$("#autosave_exists_message").hide();
		}
	</script>
	<style>
		#autosave_data {
			background-color:#bbb;
			border:1px solid #aaa;
			padding:10px;
		}
	</style>
	<div id="autosave_data" style="display:none;">
		<h2>Autosaved data</h2>
		<label for="fld_autosave_title">title</label>
		<input disabled type="text" id="fld_autosave_title" name="fld_autosave_title" value="<?=$arrayPost['autosave']['title']?>" />
		
		<label for="fld_autosave_content">Content</label>
		<textarea disabled id="fld_autosave_content" name="fld_autosave_content"><?=$arrayPost['autosave']['content']?></textarea>
		
		<label for="fld_autosave_tags">Tags</label>
		<input disabled type="text" id="fld_autosave_tags" name="fld_autosave_tags" value="<?=$arrayPost['autosave']['tags']?>" />
	</div>
	<p id="autosave_exists_message" class="info">An unsubmitted version exists, do you want to continue with this edit? <a href="#" onclick="$('#autosave_data').toggle(); return false;">hide / show content</a> <a href="#" onclick="replaceContent(); return false;">Use</a></p>
	<?php endif; ?>
	
	<form action="<?=$formAction?>" method="post" name="frm_createpost" id="frm_createpost">
	
        <div class="editpost-centre" style="width:70%; display:inline-block; vertical-align:top;">
        
		<label for="fld_posttitle">Title</label>
		<input type="text" name="fld_posttitle" id="fld_posttitle" size="50" autocomplete="off" value="<?=$fieldTitle?>" />
		
		<label for="fld_postcontent">Content</label>
        <button type="button" onclick="rbrtf_showWindow('/ajax/image_upload?blogid=<?=$arrayBlog['id']?>')" title="Insert Image"><img src="<?=CLIENT_ROOT_RESOURCES?>/icons/document_image_add_32.png" style="width:15px; height:15px;" /></button>
        
        <p style="font-size:80%;">Note - <a href="https://daringfireball.net/projects/markdown/syntax" target="_blank">Markdown</a> is supported!</p>
        <textarea name="fld_postcontent" id="fld_postcontent" style="height:30vh;"><?=$fieldContent?></textarea>
        <?php
            // htmlentities($string)
        ?>
        
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
		
		<div id="autosave_status"></div>
		
		<div class="push-right">
            <?php if($mode == 'edit'): ?>
              <input type="hidden" id="fld_postid" name="fld_postid" value="<?=$arrayPost['id']?>" />
			<?php else: ?>
			  <input type="hidden" id="fld_postid" name="fld_postid" value="0" />
            <?php endif; ?>
			<input type="hidden" name="fld_blogid" id="fld_blogid" value="<?=$arrayBlog['id']?>" />
            <input type="hidden" name="fld_posttype" value="standard" />
			<input type="submit" name="fld_submitpost" value="<?=$submitLabel?>" />
            <!--<input type="button" name="fld_previewpost" value="Preview" onclick="openPreview(); return false;" />-->
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
	
		// this doesn't work yet!!!
		$( window ).on('beforeunload', function() {
			if(!confirm('Are you sure you want to leave this page and discard changes?')) {
				return false;
			}
		});
	
		var content_changed = false;
		
		// Auto save
		var runsave = function() {
			
			if(content_changed) {
			
				jQuery.post("<?=CLIENT_ROOT_BLOGCMS?>/ajax/autosave", {
					"fld_postid": $("#fld_postid").val(),
					"fld_content": $("#fld_postcontent").val(),
					"fld_title": $("#fld_posttitle").val(),
					"fld_allowcomments": $("#fld_allowcomment").val(),
					"fld_tags": $("#fld_tags").val(),
					"fld_blogid": $("#fld_blogid").val()
					
				}, function(data) {
					if(typeof data.newpostid != "null" && typeof data.newpostid != "undefined") {
						$("#fld_postid").val(data.newpostid);
					}
					$("#autosave_status").html(data.message);
					
				}, "json");
				
				content_changed = false;
			}
		}
		
		// Run on key down of content
		$("#fld_postcontent").on("keyup", function() {
			content_changed = true;
		});
		$("#fld_title").on("keyup", function() {
			content_changed = true;
		});
		$("#fld_tags").on("keyup", function() {
			content_changed = true;
		});
		
		// Saves every 4 seconds if something has changed
		var save_interval = setInterval(runsave, 4000);
	
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
        <?php endif; ?>
        
	});
	</script>
	
<?php } ?>