{if isset($post)}

    {* We are editing the post *}
    {$formAction = "/posts/{$blog['id']}/edit/{$post['id']}/submit"}
    {$fieldTitle = $post['title']}
    {$fieldContent = $post['content']}
    {$fieldTags = str_replace("+"," ",$post['tags'])}
    {$submitLabel = 'Update'}
    {$mode = 'edit'}
    {$postdate = date('m/d/Y g:ia', strtotime($post['timestamp']))}

{else}
    {* This must be a new post *}
    {$formAction = "/posts/{$blog['id']}/new/submit"}
    {$fieldTitle = ''}
    {$fieldContent = ''}
    {$fieldTags = ''}
    {$submitLabel = 'Create'}
    {$mode = 'create'}
    {$postdate = date('m/d/Y g:ia')}
{/if}

<script type="text/javascript">
// this is now out of date as we have the rtf editor
// handy little script however...
function displayHTML() {ldelim}
    var inf = document.upload_data.fld_postcontent.value;
    win = window.open(", ", 'popup', 'toolbar = no, status = no, width = 600, height = 400');
    win.document.write("" + inf + "");
{rdelim}

// This is also not used... but was an interesting concept
function openPreview() {ldelim}
    var previewWin = window.open("/blogs/{$blog.id}/posts/post.link","_blank","height=600,width=800");
    $(previewWin).load(function() {ldelim}
        alert(typeof previewWin.document);
        previewWin.document.getElementById('comments').innerHTML = '';
    {rdelim});
{rdelim}
</script>

<div class="ui grid">
    
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/overview/{$blog['id']}", "{$blog['name']}"), 'New Post')}
        </div>
    </div>
    
    <div class="one column row">
        <div class="column">
            {viewPageHeader("{$submitLabel} Blog Post", 'add_doc.png', "{$blog['name']}")}

            {if $mode=='edit' and array_key_exists('autosave', $post)}
            <script>
                function replaceContent() {
                    $("#fld_posttitle").val($("#fld_autosave_title").val());
                    $("#fld_postcontent").val($("#fld_autosave_content").val());
                    $("#fld_tags").val($("#fld_autosave_tags").val());
                    $("#autosave_data").hide();
                    $("#autosave_exists_message").hide();
                 }
            </script>

            <div id="autosave_exists_message" class="ui yellow segment clearing">
                <p>You have an autosaved draft for this post, do you want to continue with this edit?
                <a href="#" onclick="$('#autosave_data').toggle(); return false;" class="ui basic right floated teal button">Show Content</a>
                <a href="#" onclick="$('#autosave_data').hide(); $('#autosave_exists_message').hide(); return false;" class="ui right floated teal button">No</a>
                <a href="#" onclick="replaceContent(); return false;" class="ui right floated teal button">Yes</a></p>
            </div>
            
            <div id="autosave_data" class="ui segment" style="display:none;">
                <div class="ui form">
                    <h2 class="ui heading">Autosaved Post</h2>
                    <div class="field">
                        <label for="fld_autosave_title">Title</label>
                        <input disabled class="" type="text" id="fld_autosave_title" name="fld_autosave_title" value="{$post.autosave.title}" />
                    </div>
                    <div class="field">
                        <label for="fld_autosave_content">Content</label>
                        <textarea disabled id="fld_autosave_content" name="fld_autosave_content">{$post.autosave.content}</textarea>
                    </div>
                    <div class="field">
                        <label for="fld_autosave_tags">Tags</label>
                        <input disabled type="text" id="fld_autosave_tags" name="fld_autosave_tags" value="{$post.autosave.tags}" />
                    </div>
                </div>
            </div>
            {/if}
        </div>
    </div>

    <form action="{$formAction}" method="post" name="frm_createpost" id="frm_createpost" class="two column row ui form">
        
        <div class="ten wide column">
            
            <div class="field">
                <label for="fld_posttitle">Title</label>
                <input type="text" name="fld_posttitle" id="fld_posttitle" size="50" autocomplete="off" value="{$fieldTitle}" />
            </div>
            
            <div class="field">
                <label for="fld_postcontent">Content</label>
                <button type="button" onclick="rbrtf_showWindow('/ajax/add_image?blogid={$blog.id}')" title="Insert Image"><img src="/resources/icons/document_image_add_32.png" style="width:15px; height:15px;" /></button>
                <p style="font-size:80%;">Note - <a href="https://daringfireball.net/projects/markdown/syntax" target="_blank">Markdown</a> is supported!</p>
                <textarea name="fld_postcontent" id="fld_postcontent" style="height:30vh;">{$fieldContent}</textarea>
            </div>
            
            <div class="field">
                <label for="fld_tags">Tags</label>
                <input type="text" name="fld_tags" id="fld_tags" placeholder="Enter as a Comma Seperated List" autocomplete="off" value="{$fieldTags}" />
            </div>
            
            <div id="autosave_status" class="ui positive message" style="display:none;"></div>

            {if $mode == 'edit'}
              <input type="hidden" id="fld_postid" name="fld_postid" value="{$post.id}" />
            {else}
              <input type="hidden" id="fld_postid" name="fld_postid" value="0" />
            {/if}
            
            <input type="hidden" name="fld_blogid" id="fld_blogid" value="{$blog.id}" />
            <input type="hidden" name="fld_posttype" id="fld_posttype" value="standard" />
            
            <input type="button" value="Cancel" name="goback" onclick="if(confirm('You will lose any changes made')) {ldelim} window.location = '/posts/{$blog.id}/cancelsave/' + $('#fld_postid').val(); window.content_changed = false; {rdelim}" class="ui button right floated" />
            <input type="submit" name="fld_submitpost" value="{$submitLabel}" class="ui button teal right floated" />
        </div>


        <div class="six wide column">
            
            <div class="field">
                <label for="fld_postdate">Schedule Post
                    <a href="/" onclick="alert('Set the date and time for this post to show on your blog.'); return false;">[?]</a></label>
                <div class="ui calendar" id="postdate">
                    <div class="ui input left icon">
                        <i class="calendar icon"></i>
                        <input type="text" name="fld_postdate" placeholder="Date/Time" value="{$postdate}">
                    </div>
                </div>
                <script>$('#postdate').calendar();</script>
            </div>
 
            <div class="field">
                <label for="fld_allowcomment">Allow Comments <a href="/" onclick="alert('This option will allow logged in users to post comments on your blog posts. You can control whether these are shown automatically in the blog settings.'); return false;">[?]</a></label>
                <select name="fld_allowcomment" id="fld_allowcomment">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>

            <div class="field">
                <label for="fld_draft">Action to take <a href="/" onclick="alert('Post to Blog: This post will be live on your blog for anyone that can see you blog to read. Blog security settings can be changed in the settings section.\n\nSave as Draft: The post will be saved for further editing later and will not be visible to your readers.'); return false;">[?]</a></label>
                <select name="fld_draft" id="fld_draft">
                    <option value="0">Post to Blog</option>
                    <option value="1">Save as Draft</option>
                </select>
            </div>

        </div>
    </form>


<script type="text/javascript">
var content_changed = false;
    
$(document).ready(function () {

    $(window).on('beforeunload', function()
    {ldelim}
        if(content_changed && !confirm('Are you sure you want to leave this page - you may lose changes?'))
        {ldelim}
            return false;
        {rdelim}
    {rdelim});
    
    // Auto save
    var runsave = function()
    {
        if(content_changed)
        {
            jQuery.post("/ajax/autosave",
            {
                "fld_postid": $("#fld_postid").val(),
                "fld_content": $("#fld_postcontent").val(),
                "fld_title": $("#fld_posttitle").val(),
                "fld_type": $("#fld_posttype").val(),
                "fld_allowcomments": $("#fld_allowcomment").val(),
                "fld_tags": $("#fld_tags").val(),
                "fld_blogid": $("#fld_blogid").val(),
                "csrf_token": CSRFTOKEN

            }, function(data)
            {
                if(typeof data.newpostid != "null" && typeof data.newpostid != "undefined")
                {
                    $("#fld_postid").val(data.newpostid);
                }
                $("#autosave_status").html(data.message);
                $("#autosave_status").show();

            }, "json");
        }
    }

    // Run on key down of content
    $("#fld_postcontent").on("keyup", function() { content_changed = true; });
    $("#fld_title").on("keyup", function() { content_changed = true; });
    $("#fld_tags").on("keyup", function() { content_changed = true; });

    // Saves every 10 seconds if something has changed
    var save_interval = setInterval(runsave, 5000);

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

        if(post_title.length == 0) {
            alert("Please enter a title");
            return false;
        }
    
        $.get({ldelim}url:url, async:false{rdelim}, function(data)
        {ldelim}
            if(data !== "false") alert("Validation Failed - Title needs to be unique for this blog!");
            return false;
        {rdelim});

        // Update the wiki code
        /*
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
        */
    
        return true;
    {rdelim});

    {if $mode == 'edit'}
        // Apply Defaults
        $("#fld_draft").val({$post.draft});
        $("#fld_allowcomment").val({$post.allowcomments});
    {/if}

});
</script>
</div>