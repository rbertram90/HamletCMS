<?php
/**
 NO LONGER USED - See 'standard post' - does both new and edit
**/


use \Michelf\Markdown;

function viewEditPostForm($pObjPost, $pobjBlog) { ?>

	<div class="crumbtrail"><a href="<?=CLIENT_ROOT_BLOGCMS?>">Home</a><a href='<?=CLIENT_ROOT_BLOGCMS?>/overview/<?=$pObjPost['blog_id'] ?>'><?=$pobjBlog['name']?></a><a href="<?=CLIENT_ROOT_BLOGCMS?>/posts/<?=$pobjBlog['id']?>">Manage Posts</a><a>Edit Post</a></div>

	<img src="<?=CLIENT_ROOT?>/resources/icons/64/pen.png" class="settings-icon" /><h1 class="settings-title" style="margin-top:0px;">Editing Post<br><span class="subtitle">&quot;<?=$pObjPost['title']?>&quot;
	<?php if($pObjPost['draft'] == 1) echo "<em>(Draft)</em>"; ?></span></h1>

	<form action="<?=CLIENT_ROOT_BLOGCMS?>/posts/<?=$pobjBlog['id']?>/edit/<?=$pObjPost['id']?>/submit" method="post" name="frm_editpost" id="frm_editpost">
	
		<label for="fld_posttitle">Title</label>
		<input type="text" name="fld_posttitle" id="fld_posttitle" value="<?=$pObjPost['title']?>" size="50" autocomplete="off" />
		        		
        <!-- NON Fancy Editor! -->
		<label for="fld_postcontent">Content</label>
        
        <button type="button" onclick="rbrtf_showWindow('<?=CLIENT_ROOT_BLOGCMS?>/ajax/image_upload?blogid=<?=$pobjBlog['id']?>')" title="Insert Image"><img src="<?=CLIENT_ROOT_RESOURCES?>/icons/document_image_add_32.png" style="width:15px; height:15px;" /></button>
        
        <p style="font-size:80%;">Note - <a href="https://daringfireball.net/projects/markdown/syntax" target="_blank">Markdown</a> is supported!</p>
        <textarea name="fld_postcontent" id="fld_postcontent" rows="10"><?= $pObjPost['content']?></textarea>
        
<!--
        <div id="postcontent"></div>

		<script type="text/javascript">
		$("#postcontent").rbrtf({
			"urlroot": "../../../..",
			"content": "<?= str_replace("\n","\ ",trim(Markdown::defaultTransform($pObjPost['content']))) ?>",
			"rawvalueid": "fld_postcontent",
			"customControls": "  <button onclick=\"rbrtf_showWindow('../../ajax/image_upload.php?blogid=<?=$pobjBlog['id']?>')\" title=\"Insert Image\"><img src=\"../../../../resources/icons/document_image_add_32.png\" style=\"width:15px; height:15px;\" /></button>"
		});
		</script>
-->
		
		<label for="fld_tags">Tags</label>
		<input type="text" name="fld_tags" id="fld_tags" value="<?=str_replace("+"," ",$pObjPost['tags'])?>" placeholder="Enter as a Comma Seperated List" autocomplete="off" />
		
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
			<input type="hidden" name="fld_postid" value="<?=$pObjPost['id']?>" />
			<input type="hidden" name="fld_blogid" value="<?=$pobjBlog['id']?>" />
			<input type="submit" name="submit_update" value="Update" />
		</div>
	</form>

    <script type="text/javascript">
        var submitForm = function() {
		    document.frm_editpost.submit();
		};
                
       /*  $(document).ready(function () {          
            
            // Handle form submission
            $("#frm_editpost").submit(function() {
			
                if($(".rbrtf-rtfinput").is(":visible")) {
				    
                    // removeEmptyElements($('.rbrtf-rtfinput'));
                    
                    // Update wiki fields before submitting
                    var htmlcontent = $(".rbrtf-editor .rbrtf-rtfinput").html();
					
                    jQuery.get("<?=CLIENT_ROOT?>/core/ajax/ajax_viewWikiMarkup.php", {content:htmlcontent}, function(data) {
				        $(".rbrtf-editor .rbrtf-wikiinput").val(data);
                        submitForm();
			        });
					
                    // Wait for the ajax to submit the form on completion
                    return false;
                }
                return true;
            });
        */
        
$(document).ready(function () { 
            // Apply Defaults
            $("#fld_draft").val(<?=$pObjPost['draft']?>);
			$("#fld_allowcomment").val(<?=$pObjPost['allowcomments']?>);
            
});
            // Default to showing RTF Editor
            //rbrtf_showRTF();
       //  });
    </script>

<?php } ?>