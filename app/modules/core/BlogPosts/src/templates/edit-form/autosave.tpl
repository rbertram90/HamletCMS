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
    var runsave = function() {

        // Check a change has been made
        if (!content_changed) return;

        // Build up the fields object with properties to
        // pass to the server to be saved
        var formData = {
            token: CSRFTOKEN
        };

        $(".post-data-field").each(function() {
            var key, type;

            if ($(this).data('key')) key = $(this).data('key');
            else key = $(this).attr('name');

            if ($(this).data('type')) type = $(this).data('type');
            else type = 'string';

            switch (type) {
                case 'checkbox':
                    formData[key] = this.checked;
                    break;

                case 'int':
                    formData[key] = parseInt($(this).val());
                    break;

                default:
                case 'string':
                    formData[key] = $(this).val();
                    break;
            }
        });
        
        // Collect form data and send to server
        jQuery.post("/api/posts/autosave", formData, function(data) {
            // Populate post id field if just created
            if(typeof data.newpostid != "null" && typeof data.newpostid != "undefined") {
                $("#post_id").val(data.newpostid);
            }
            $("#autosave_status").html(data.message);
            $("#autosave_status").show();

            content_changed = false;

        }, "json");
    };

    // Run on key down of content
    $("#post_content").on("keyup", function() { content_changed = true; });
    $("#post_title").on("keyup", function() { content_changed = true; });
    $("#tags").on("keyup", function() { content_changed = true; });

    // Try and save every 5 seconds
    var save_interval = setInterval(runsave, 5000);
});
</script>