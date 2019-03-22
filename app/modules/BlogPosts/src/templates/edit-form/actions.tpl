<div id="autosave_status" class="ui positive message" style="display:none;"></div>

{if $mode == 'edit'}
    <input type="hidden" id="post_id" name="post_id" value="{$post->id}">
{else}
    <input type="hidden" id="post_id" name="post_id" value="0">
{/if}

<input type="button" value="Cancel" id="cancel_create_post" name="goback" onclick="if(confirm('You will lose any changes made')) {ldelim} window.location = getCancelLocation(); window.content_changed = false; {rdelim}" class="ui button right floated">

<input type="submit" name="submit_create_post" id="submit_create_post" value="{$submitLabel}" class="ui button teal right floated">


<div class="ui basic modal" id="post_save_success">
  <div class="ui icon huge header">
    <i class="green check icon"></i>
    Post saved!
  </div>
  <div class="content" style="text-align:center;">
    What would you like to do next?
  </div>
  <div class="actions" style="text-align:center;">
    <a href="" class="large ui basic inverted teal button" id="view_post_link"><i class="eye icon"></i> View</a>
    <a href="" class="large ui basic inverted teal button" id="edit_post_link"><i class="pencil icon"></i> Amend</a>
    <a href="/cms/posts/create/{$blog->id}" class="large ui basic inverted teal button"><i class="plus icon"></i> Create another</a>
    <a href="/cms/posts/manage/{$blog->id}" class="large ui basic inverted teal button"><i class="copy outline icon"></i>Manage posts</a>
  </div>
</div>


<script>
    // Get where to redirect to when the cancel
    // button is pressed. Will be different depending
    // on if the autosave has run
    var getCancelLocation = function () {
        var postID = $('#post_id').val();

        if (postID > 0) {
            return '/cms/posts/cancelsave/' + postID;
        }
        else {
            return '/cms/blog/overview/{$blog->id}';
        }
    };

    var disableUnloadMessage = function () {
        $(window).unbind('beforeunload');
    };
    var enableUnloadMessage = function () {
        $(window).on('beforeunload', function() {
            if(content_changed && !confirm('Are you sure you want to leave this page - you may lose changes?')) {
                return false;
            }
        });
    };

    var disableForm = function () {
        $("#submit_create_post").prop("disabled", true);
        $("#cancel_create_post").prop("disabled", true);
        content_changed = false; // don't autosave again
        disableUnloadMessage();
    };

    var enableForm = function () {
        $("#submit_create_post").prop("disabled", false);
        $("#cancel_create_post").prop("disabled", false);
        enableUnloadMessage();
    };

    // Handle form submission
    $("#form_create_post").submit(function(event) {

        // Update UI
        disableForm();
        $(".form_status").html("Saving post...").show(300);

        event.preventDefault();

        var formData = {
            blogID: {$blog->id},
            postID: parseInt($("#post_id").val()),
            title: $("#post_title").val(),
            summary: $("#summary").val(),
            content: $("#post_content").val(),
            tags: $("#post_tags").val(),
            type: $("#post_type").val(),
            date: $("#post_date").val(),
            draft: parseInt($("#draft").val()),
            teaserImage: false
        };

        if ($("#allow_comment")) {
            formData.comments = parseInt($("#allow_comment").val());
        }

        if ($("#teaser_image_image img").length > 0) {
            var imageSrc = $("#teaser_image_image img").attr('src');
            srcSplit = imageSrc.split('/');
            formData.teaserImage = srcSplit.pop();
        }

        // Allow custom modules to add to data
        if (typeof getFormData == 'function') { 
            formData = getFormData();
        }
        else {
            console.log('Error: function getFormData has not been defined');
            return false;
        }

        // Quick client side validation
        if (formData.title.length == 0) {
            $(".form_status").html("Please add a title").addClass("ui message error");
            enableForm();
            return;
        }

        if (formData.postID > 0) {
            var saveURL = '/api/posts/update';
        }
        else {
            var saveURL = '/api/posts/create';
        }

        $.ajax({ url: saveURL, async: false, type: 'POST', data: formData }).done(function (data) {
            if (data.success) {
                // window.location = '/cms/posts/manage/' + formData.blogID;
                $('#post_save_success').modal('setting', 'closable', false).modal('show');
                $('#edit_post_link').attr('href', '/cms/posts/edit/' + data.post.id);
                $('#view_post_link').attr('href', '/blogs/{$blog->id}/posts/' + data.post.link);
            }
            enableForm();

        }).fail(function (jqXHR, textStatus, errorThrown) {
            data = JSON.parse(jqXHR.responseText);
            $(".form_status").html("Save failed: " + data.errorMessage).addClass("ui message error");
            enableForm();
        });
        
    });
</script>