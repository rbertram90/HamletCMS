{if isset($post)}
    {* We are editing the post *}
    {$formAction = "/cms/posts/edit/{$post->id}"}
    {$fieldTags = str_replace("+"," ",$post->tags)}
    {$submitLabel = 'Update'}
    {$mode = 'edit'}
    {$postdate = date('d/m/Y H:i', strtotime($post->timestamp))}
{else}
    {* This must be a new post *}
    {$formAction = "/cms/posts/create/{$blog->id}/standard"}
    {$fieldTags = ''}
    {$submitLabel = 'Create'}
    {$mode = 'create'}
    {$postdate = date('d/m/Y H:i')}
{/if}

<div class="ui grid">
    <div class="form_status"></div>

    <form action="{$formAction}" method="POST" name="form_create_post" id="form_create_post" class="two column row ui form">
        
        <div class="ten wide column">
            {include 'edit-form/title.tpl'}

            {include 'edit-form/teaser-summary.tpl'}

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
            
            {include 'edit-form/tags.tpl'}
            
            <input type="hidden" name="post_type" id="post_type" value="standard" class="post-data-field" data-key="type">

            {* Submit button + hidden fields *}
            {include 'edit-form/actions.tpl'}
        </div>

        <div class="six wide column">
            {include 'edit-form/post-date.tpl'}

            {include 'edit-form/url.tpl'}
 
            {include 'edit-form/custom-fields.tpl'}

            {include 'edit-form/published.tpl'}

            {include 'edit-form/teaser-image.tpl'}
        </div>
    </form>

<script>
var content_changed = false;
var isDarkMode = false;

$(document).ready(function () {

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
        if(content_changed && !confirm('Are you sure you want to leave this page - you may lose changes?')) {
            return false;
        }
    });
    
});
</script>
</div>