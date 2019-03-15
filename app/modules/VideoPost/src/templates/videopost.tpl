{if isset($post)}
    {* We are editing the post *}
    {$formAction = "/cms/posts/edit/{$post->id}"}
    {$fieldTags = str_replace("+", " ", $post->tags)}
    {$submitLabel = 'Update'}
    {$postdate = date('m/d/Y g:ia', strtotime($post->timestamp))}
    {$mode = 'edit'}
{else}
    {* This must be a new post *}
    {$formAction = "/cms/posts/create/{$blog->id}/video"}
    {$fieldTags = ''}
    {$submitLabel = 'Create'}
    {$postdate = date('m/d/Y g:ia')}
    {$mode = 'create'}
{/if}

<div class="ui grid">
    
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/cms/blog/overview/{$blog->id}", "{$blog->name}"), 'New Post')}
        </div>
    </div>
    
    <div class="one column row">
        <div class="column">
            {viewPageHeader("{$submitLabel} Blog Post", 'edit outline', "{$blog->name}")}

            {include 'edit-form/autosave.tpl'}
        </div>
    </div>

    <div class="form_status"></div>

    <form action="{$formAction}" method="post" name="form_create_post" id="form_create_post" class="two column row ui form">
        
        <div class="ten wide column">
            {include 'edit-form/title.tpl'}

            {include 'edit-form/url.tpl'}

            {include 'edit-form/teaser-summary.tpl'}

            <div class="field"> 
                <label for="video_source">Video Source</label>
                <select name="video_source" id="video_source" class="ui dropdown">
                    <option value="youtube">YouTube</option>
                    <option value="vimeo">Vimeo</option>
                </select>
            </div>
            <div class="field"> 
                <label for="video_id">Video ID <a href="#" onclick="alert('Youtube ID are found in the URL youtube.com/user/?v={ldelim}URL{rdelim}'); return false;">[?]</a></label>
                <input type="text" name="video_id" placeholder="Enter a YouTube or Vimeo Video ID" id="video_id" size="50" autocomplete="off" value="{$post->videoid}" />
            </div>

            <div class="field">
                <label for="post_content">Content</label>
                <button type="button" id="upload_post_image" class="ui icon button" title="Insert Image">
                    <i class="camera icon"></i>
                </button>
                <p style="font-size:80%;">Note - <a href="https://daringfireball.net/projects/markdown/syntax" target="_blank">Markdown</a> is supported!</p>
                <textarea name="post_content" id="post_content" style="height:30vh;">{$post->content}</textarea>
            </div>
            
            {include 'edit-form/tags.tpl'}
            
            <input type="hidden" name="post_type" id="post_type" value="video">

            {* Submit button + hidden fields *}
            {include 'edit-form/actions.tpl'}
        </div>

        <div class="six wide column">
            {include 'edit-form/post-date.tpl'}
 
            {include 'edit-form/custom-fields.tpl'}

            {include 'edit-form/published.tpl'}

            {include 'edit-form/teaser-image.tpl'}
        </div>
    </form>

<script>
var content_changed = false;

var addToFormData = function(postData) {
    postData.videoid = $("#video_id").val();
    postData.videosource = $("#video_source").val();
    return postData;
};

$(document).ready(function () {
    $('.ui.dropdown').dropdown();

    $("#upload_post_image").click(function() {
        $('.ui.upload_image_modal').load('/cms/files/fileselect/{$blog->id}', { 'csrf_token': CSRFTOKEN }, function() {
            $(this).modal('show');
        });
    });

    $(window).on('beforeunload', function() {
        if(content_changed && !confirm('Are you sure you want to leave this page - you may lose changes?')) {
            return false;
        }
    });

    {if $mode == 'edit'}
        $('#video_source').dropdown('set selected', '{$post->videosource}');
    {/if}
});

</script>
</div>