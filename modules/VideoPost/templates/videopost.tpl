{if isset($post)}
    {* We are editing the post *}
    {$formAction = "/cms/posts/edit/{$post->id}"}
    {$fieldTags = str_replace("+", " ", $post->tags)}
    {$submitLabel = 'Update'}
    {$mode = 'edit'}
    {$postdate = date('d/m/Y H:i', strtotime($post->timestamp))}
{else}
    {* This must be a new post *}
    {$formAction = "/cms/posts/create/{$blog->id}/video"}
    {$fieldTags = ''}
    {$submitLabel = 'Create'}
    {$mode = 'create'}
    {$postdate = date('d/m/Y H:i')}
{/if}
<style>
.ui.grid.write-post {
    margin-top: 0;
}
</style>
<div class="ui grid write-post">
    <div class="form_status"></div>
    {include 'edit-form/autosave.tpl'}

    <form action="{$formAction}" method="POST" name="form_create_post" id="form_create_post" class="two column row ui form">
        
        <div class="ten wide column">
            {include 'edit-form/title.tpl'}

            <div class="field"> 
                <label for="video_source">Video Source</label>
                <select name="video_source" id="video_source" class="ui dropdown" class="post-data-field" data-key="videosource">
                    <option value="youtube">YouTube</option>
                    <option value="vimeo">Vimeo</option>
                </select>
            </div>

            <div class="field"> 
                <label for="video_id">Video ID <a href="#" onclick="alert('Youtube ID are found in the URL youtube.com/user/?v={ldelim}URL{rdelim}'); return false;">[?]</a></label>
                <input type="text" name="video_id" placeholder="Enter a YouTube or Vimeo Video ID" id="video_id" size="50" autocomplete="off" value="{if isset($post)}{$post->videoid}{/if}"  class="post-data-field" data-key="videoid">
            </div>

            <div class="field">
                <label for="post_content">Content</label>
                <button type="button" id="upload_post_image" class="ui icon button" title="Insert Image" data-no-spinner="true">
                    <i class="camera icon"></i>
                </button>
                <button type="button" id="dark_mode_toggle" class="ui icon button" title="Toggle dark mode" data-no-spinner="true">
                    <i class="moon icon"></i>
                </button>
                <textarea name="post_content" id="post_content" style="height:30vh;" class="post-data-field" data-key="content">{if isset($post)}{$post->content}{/if}</textarea>
                <p style="font-size:80%;"><a href="https://daringfireball.net/projects/markdown/syntax" target="_blank">Markdown</a> is supported!</p>
            </div>
                        
            <input type="hidden" name="post_type" id="post_type" value="video" class="post-data-field" data-key="type">

            {* Submit button + hidden fields *}
            {include 'edit-form/actions.tpl'}
        </div>

        <div class="six wide column">
            {include 'edit-form/post-date.tpl'}

            {include 'edit-form/url.tpl'}

            {include 'edit-form/teaser-summary.tpl'}

            {include 'edit-form/tags.tpl'}
 
            {include 'edit-form/custom-fields.tpl'}

            {include 'edit-form/published.tpl'}

            {include 'edit-form/teaser-image.tpl'}
        </div>
    </form>

<script>
var content_changed = false;
var isDarkMode = false;

$("#video_source").on("keyup", function() { content_changed = true; });
$("#video_id").on("keyup", function() { content_changed = true; });
/*
var getFormData = function() {
    return {
        postID: parseInt($("#post_id").val()),
        blogID: {$blog->id},
        content: $("#post_content").val(),
        title: $("#post_title").val(),
        summary: $("#summary").val(),
        overrideLink: document.getElementById('field_override_url').checked,
        link: $("#post_url").val(),
        type: $("#post_type").val(),
        tags: $("#post_tags").val(),
        draft: parseInt($("#draft").val()),
        date: $("#post_date").val(),
        videoid: $("#video_id").val(),
        videosource: $("#video_source").val(),
        teaserImage: $("input[name='teaser_image']").val(),
        token: CSRFTOKEN
    };
};
*/
$(document).ready(function () {
    $('.ui.dropdown').dropdown();

    $("#upload_post_image").click(function() {
        $('.ui.upload_image_modal').load('/cms/files/fileselect/{$blog->id}', { 'csrf_token': CSRFTOKEN }, function() {
            $(this).modal('show');
        });
    });

    $("#dark_mode_toggle").click(function() {
        if (isDarkMode) {
            $("#post_content").removeClass('dark-mode');
            $(this).find('i').removeClass('sun').addClass('moon');
            isDarkMode = false;
        }
        else {
            $("#post_content").addClass('dark-mode');
            $(this).find('i').removeClass('moon').addClass('sun');
            isDarkMode = true;
        }
    });

    $(window).on('beforeunload', function() {
        if (content_changed && !confirm('Are you sure you want to leave this page - you may lose changes?')) {
            return false;
        }
    });

    {if $mode == 'edit'}
        $('#video_source').dropdown('set selected', '{$post->videosource}');
    {/if}
});

</script>
</div>