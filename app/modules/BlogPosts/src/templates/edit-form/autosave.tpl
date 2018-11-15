{if $mode=='edit' and array_key_exists('autosave', $post)}
<script>
    function replaceContent() {
        $("#post_title").val($("#autosave_title").val());
        $("#post_content").val($("#autosave_content").val());
        $("#post_tags").val($("#autosave_tags").val());
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
            <label for="autosave_title">Title</label>
            <input disabled class="" type="text" id="autosave_title" name="autosave_title" value="{$post.autosave.title}" />
        </div>
        <div class="field">
            <label for="autosave_content">Content</label>
            <textarea disabled id="autosave_content" name="autosave_content">{$post.autosave.content}</textarea>
        </div>
        <div class="field">
            <label for="autosave_tags">Tags</label>
            <input disabled type="text" id="autosave_tags" name="autosave_tags" value="{$post.autosave.tags}" />
        </div>
    </div>
</div>
{/if}

<script>
$(document).ready(function() {
    
    // Auto save
    var runsave = function()
    {
        if(content_changed)
        {
            jQuery.post("/api/posts/autosave",
            {
                "postID": parseInt($("#post_id").val()),
                "blogID": {$blog.id},
                "content": $("#post_content").val(),
                "title": $("#post_title").val(),
                "type": $("#post_type").val(),
                "comments": parseInt($("#allow_comment").val()),
                "tags": $("#post_tags").val(),
                "token": CSRFTOKEN

            }, function(data)
            {
                if(typeof data.newpostid != "null" && typeof data.newpostid != "undefined")
                {
                    $("#post_id").val(data.newpostid);
                }
                $("#autosave_status").html(data.message);
                $("#autosave_status").show();

                content_changed = false;

            }, "json");
        }
    }

    // Run on key down of content
    $("#post_content").on("keyup", function() { content_changed = true; });
    $("#post_title").on("keyup", function() { content_changed = true; });
    $("#tags").on("keyup", function() { content_changed = true; });

    // Saves every 10 seconds if something has changed
    var save_interval = setInterval(runsave, 5000);

});
</script>