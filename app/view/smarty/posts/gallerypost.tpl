{* want to use front-end gallery from http://galleria.io/ *}

{* note much of this code needs putting into an include as it is the same for gallery, video and standard posts... *}

{if isset($post)}

    {* We are editing the post *}
    {$formAction = "/posts/{$blog['id']}/edit/{$post['id']}/submit"}
    {$fieldTitle = $post['title']}
    {$fieldContent = $post['content']}
    {$fieldTags = str_replace("+"," ",$post['tags'])}
    {$submitLabel = 'Update'}
    {$mode = 'edit'}
    {$postdate_d = substr($post['timestamp'], 8, 2)}
    {$postdate_m = substr($post['timestamp'], 5, 2)}
    {$postdate_y = substr($post['timestamp'], 0, 4)}
    {$postdate_h = substr($post['timestamp'], 11, 2)}
    {$postdate_i = substr($post['timestamp'], 14, 2)}

{else}
    {* This must be a new post *}
    {$formAction = "/posts/{$blog['id']}/new/submit"}
    {$fieldTitle = ''}
    {$fieldContent = ''}
    {$fieldTags = ''}
    {$submitLabel = 'Create'}
    {$mode = 'create'}
    {$postdate_d = date('d')}
    {$postdate_m = date('m')}
    {$postdate_y = date('Y')}
    {$postdate_h = date('H')}
    {$postdate_i = date('i')}
{/if}


{viewCrumbtrail(array("/overview/{$blog['id']}", "{$blog['name']}"), 'New Post')}
{viewPageHeader("{$submitLabel} Blog Post <span style='color:green;'>+Gallery</span>", 'add_doc.png', "{$blog['name']}")}


{if $mode=='edit' and array_key_exists('autosave', $post)}

	<script>
		function replaceContent()
        {ldelim}
			$("#fld_posttitle").val($("#fld_autosave_title").val());
			$("#fld_postcontent").val($("#fld_autosave_content").val());
			$("#fld_tags").val($("#fld_autosave_tags").val());
			$("#autosave_data").hide();
			$("#autosave_exists_message").hide();
		{rdelim}
	</script>
    
	<div id="autosave_data" style="display:none; background-color:#bbb; border:1px solid #aaa; padding:10px;">
        
		<h2>Autosaved data</h2>
        
		<label for="fld_autosave_title">title</label>
		<input disabled type="text" id="fld_autosave_title" name="fld_autosave_title" value="{$post.autosave.title}" />
		
		<label for="fld_autosave_content">Content</label>
		<textarea disabled id="fld_autosave_content" name="fld_autosave_content">{$post.autosave.content}</textarea>
		
		<label for="fld_autosave_tags">Tags</label>
		<input disabled type="text" id="fld_autosave_tags" name="fld_autosave_tags" value="{$post.autosave.tags}" />
        
	</div>

	<p id="autosave_exists_message" class="info">An unsubmitted version exists, do you want to continue with this edit? <a href="#" onclick="$('#autosave_data').toggle(); return false;">hide / show content</a> <a href="#" onclick="replaceContent(); return false;">Use</a></p>

{/if}
	
<form action="{$formAction}" method="post" name="frm_createpost" id="frm_createpost">

    <div class="editpost-centre" style="width:70%; display:inline-block; vertical-align:top;">

    <label for="fld_posttitle">Title</label>
    <input type="text" name="fld_posttitle" id="fld_posttitle" size="50" autocomplete="off" value="{$fieldTitle}" />
    
    <script>
        function updateImageList() {ldelim}
            var imageList = "";
            
            $('.galleryimagepath').each(function() {ldelim}
                imageList += $(this).val() + ",";
            {rdelim});
            
            $('#fld_gallery_imagelist').val(imageList);
        {rdelim}
        
        {if $mode == 'edit'}
            var fieldCount = {count($post.gallery_imagelist)};
        {else}
            var fieldCount = 0;
        {/if}
        
        function addNewField() {ldelim}
            $("#galleryNewImages").append('<div id="galleryimages' + fieldCount + 'holder"><div style="" class="galleryimage" id="galleryimages' + fieldCount + '"></div><input type="hidden" name="galleryimages[' + fieldCount + ']" value="" id="galleryimages' + fieldCount + 'path" class="galleryimagepath" /><button type="button" onclick="rbrtf_showWindow(\'/ajax/add_image?blogid={$blog.id}&elemid=galleryimages' + fieldCount + '&format=gallery\')" title="Insert Image">(*) Select Image</button><button type="button" onclick="removeImage(\'galleryimages' + fieldCount + 'holder\');">(-) Remove</button></div>');
            fieldCount++;
        {rdelim}
         
         function removeImage(elemid) {ldelim}
            $('#' + elemid).remove();
            updateImageList();
         {rdelim}
    </script>
        
    <style>
        .galleryimage {ldelim}width:100px; height:100px; background-size:cover;{rdelim}
    </style>
    
    <label for="fld_posttitle">Images</label>
    {* Gallery Fields Go Here!!! *}
    {if $mode == 'edit'}
        {$i = 0}
        {foreach $post.gallery_imagelist as $galleryimage}
        {if strlen($galleryimage) > 0}
          <div id="galleryimages{$i}holder">
            <div style="background-image:url('{$galleryimage}');" class="galleryimage" id="galleryimages{$i}"></div>
            <input type="hidden" name="galleryimages[0]" value="{$galleryimage}" id="galleryimages{$i}path" class="galleryimagepath" />
            <button type="button" onclick="rbrtf_showWindow('/ajax/add_image?blogid={$blog.id}&elemid=galleryimages{$i}&format=gallery')" title="Insert Image">(*) Change</button>
            <button type="button" onclick="removeImage('galleryimages{$i}holder');">(-) Remove</button>
            {$i = $i + 1}
          </div>
        {/if}
        {/foreach}
    {/if}
        
    <div id="galleryNewImages">
    </div>
        
    <button onclick="addNewField(); return false;" type="button">(+) Add New</button>
    
    <input type="hidden" name="fld_gallery_imagelist" id="fld_gallery_imagelist" value="" />
    
    
    <label for="fld_postcontent">Content</label>
    <button type="button" onclick="rbrtf_showWindow('{$clientroot_blogcms}/ajax/add_image?blogid={$blog.id}')" title="Insert Image"><img src="/resources/icons/document_image_add_32.png" style="width:15px; height:15px;" /></button>

    <p style="font-size:80%;">Note - <a href="https://daringfireball.net/projects/markdown/syntax" target="_blank">Markdown</a> is supported!</p>

    <textarea name="fld_postcontent" id="fld_postcontent" style="height:30vh;">{$fieldContent}</textarea>

    <label for="fld_tags">Tags</label>
    <input type="text" name="fld_tags" id="fld_tags" placeholder="Enter as a Comma Seperated List" autocomplete="off" value="{$fieldTags}" />

    <div id="autosave_status"></div>

    <div class="push-right">
        {if $mode == 'edit'}
          <input type="hidden" id="fld_postid" name="fld_postid" value="{$post.id}" />
        {else}
          <input type="hidden" id="fld_postid" name="fld_postid" value="0" />
        {/if}
        <input type="hidden" name="fld_blogid" id="fld_blogid" value="{$blog.id}" />
        
        {* Make sure this is changed for different post type!!! *}
        <input type="hidden" name="fld_posttype" value="gallery" />
        
        <input type="submit" name="fld_submitpost" value="{$submitLabel}" />
        <!--<input type="button" name="fld_previewpost" value="Preview" onclick="openPreview(); return false;" />-->
        <input type="button" value="Cancel" name="goback" onclick="window.history.back()" />
    </div>

    </div><div class="editpost-options" style="width:30%; display:inline-block; vertical-align:top;">

    <label for="fld_postdate">Schedule Post<br><span style="font-weight:normal; font-style:italic;">Set custom date and time to show the post on your blog.</span></label>
    <input type="text" name="fld_postdate_d" style="width:26px;" value="{$postdate_d}" /> /
    <input type="text" name="fld_postdate_m" style="width:26px;" value="{$postdate_m}" /> /
    <input type="text" name="fld_postdate_y" style="width:40px;" value="{$postdate_y}" />
    &nbsp;&nbsp;
    <input type="text" name="fld_postdate_h" style="width:26px;" value="{$postdate_h}" /> : 
    <input type="text" name="fld_postdate_i" style="width:26px;" value="{$postdate_i}" />


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
$(document).ready(function ()
{ldelim}

    // this doesn't work yet!!!
    $(window).on('beforeunload', function()
    {ldelim}
        if(!confirm('Are you sure you want to leave this page and discard changes?'))
        {ldelim}
            return false;
        {rdelim}
    {rdelim});

    var content_changed = false;

    // Auto save
    var runsave = function()
    {ldelim}

        if(content_changed)
        {ldelim}

            jQuery.post("/ajax/autosave",
            {ldelim}
                "fld_postid": $("#fld_postid").val(),
                "fld_content": $("#fld_postcontent").val(),
                "fld_title": $("#fld_posttitle").val(),
                "fld_allowcomments": $("#fld_allowcomment").val(),
                "fld_tags": $("#fld_tags").val(),
                "fld_blogid": $("#fld_blogid").val()

            {rdelim}, function(data)
            {ldelim}
                if(typeof data.newpostid != "null" && typeof data.newpostid != "undefined")
                {ldelim}
                    $("#fld_postid").val(data.newpostid);
                {rdelim}
                $("#autosave_status").html(data.message);

            {rdelim}, "json");

            content_changed = false;
        {rdelim}
    {rdelim}

    // Run on key down of content
    $("#fld_postcontent").on("keyup", function()
    {ldelim}
        content_changed = true;
    {rdelim});
    $("#fld_title").on("keyup", function()
    {ldelim}
        content_changed = true;
    {rdelim});
    $("#fld_tags").on("keyup", function()
    {ldelim}
        content_changed = true;
    {rdelim});

    // Saves every 4 seconds if something has changed
    var save_interval = setInterval(runsave, 4000);

    // Function to submit form
    var submitForm = function () {ldelim}
        document.frm_createpost.submit();
    {rdelim};

    // Handle form submission
    $("#frm_createpost").submit(function()
    {ldelim}

        $(window).unbind('beforeunload');

        // Check that the post title is unique
        var post_title = $("#fld_posttitle").val();
        var blog_id = {$blog.id};
        var url = "/ajax/check_title?blog_id=" + blog_id + "&post_title=" + post_title;

        $.get(url, function(data)
        {ldelim}
            if(data !== "false") alert("Validation Failed - Title needs to be unique for this blog!");
            return false;
        {rdelim});

        // Update the wiki code
        if($(".rbrtf-rtfinput").is(":visible")) {ldelim}
            // Update wiki fields before submitting
            var htmlcontent = $(".rbrtf-editor .rbrtf-rtfinput").html();
            jQuery.get("/ajax/ajax_viewWikiMarkup.php", {ldelim}content:htmlcontent{rdelim},  function(data) {ldelim}
                $(".rbrtf-editor .rbrtf-wikiinput").val(data);
                submitForm();
            {rdelim});
            // Wait for the ajax to submit the form on completion
            return false;
        {rdelim}
        return true;
    {rdelim});

    {if $mode == 'edit'}
        // Apply Defaults
        $("#fld_draft").val({$post.draft});
        $("#fld_allowcomment").val({$post.allowcomments});
    {/if}

{rdelim});
</script>