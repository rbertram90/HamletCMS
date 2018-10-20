{if isset($post)}

    {* We are editing the post *}
    {$formAction = "/cms/posts/edit/{$post['id']}"}
    {$fieldTitle = $post['title']}
    {$fieldContent = $post['content']}
    {$teaserImage = $post['teaser_image']}
    {$fieldTags = str_replace("+"," ",$post['tags'])}
    {$submitLabel = 'Update'}
    {$mode = 'edit'}
    {$postdate = date('m/d/Y g:ia', strtotime($post['timestamp']))}

{else}
    {* This must be a new post *}
    {$formAction = "/cms/posts/create/{$blog.id}/standard"}
    {$fieldTitle = ''}
    {$fieldContent = ''}
    {$teaserImage = ''}
    {$fieldTags = ''}
    {$submitLabel = 'Create'}
    {$mode = 'create'}
    {$postdate = date('m/d/Y g:ia')}
{/if}

<div class="ui grid">
    
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/cms/blog/overview/{$blog['id']}", "{$blog['name']}"), 'New Post')}
        </div>
    </div>
    
    <div class="one column row">
        <div class="column">
            {viewPageHeader("{$submitLabel} Blog Post", 'edit outline', "{$blog['name']}")}

            {include 'edit-form/autosave.tpl'}
        </div>
    </div>

    <div class="form_status"></div>

    <form action="{$formAction}" method="post" name="form_create_post" id="form_create_post" class="two column row ui form">
        
        <div class="ten wide column">
            {include 'edit-form/title.tpl'}

            <div class="field">
                <label for="post_content">Content</label>
                <button type="button" id="upload_post_image" class="ui icon button" title="Insert Image">
                    <i class="camera icon"></i>
                </button>
                <p style="font-size:80%;">Note - <a href="https://daringfireball.net/projects/markdown/syntax" target="_blank">Markdown</a> is supported!</p>
                <textarea name="post_content" id="post_content" style="height:30vh;">{$fieldContent}</textarea>
            </div>
            
            {include 'edit-form/tags.tpl'}
            
            {* Submit button + hidden fields *}
            {include 'edit-form/actions.tpl'}
        </div>

        <div class="six wide column">
            {include 'edit-form/post-date.tpl'}
 
            {include 'edit-form/allow-comments.tpl'}

            {include 'edit-form/published.tpl'}

            {include 'edit-form/teaser-image.tpl'}
        </div>
    </form>

<script>
var content_changed = false;
window.postTitleIsValid = false;
    
$(document).ready(function () {

    $("#upload_post_image").click(function() {
        $('.ui.upload_image_modal').load('/cms/files/fileselect/{$blog.id}', { 'csrf_token': CSRFTOKEN }, function() {
            $(this).modal('show');
        });
    });

    $(window).on('beforeunload', function() {
        if(content_changed && !confirm('Are you sure you want to leave this page - you may lose changes?')) {
            return false;
        }
    });
    
});
</script>
</div>