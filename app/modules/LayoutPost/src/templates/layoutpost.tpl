{if isset($post)}
    {* We are editing the post *}
    {$formAction = "/cms/posts/edit/{$post->id}"}
    {$fieldTags = str_replace("+", " ", $post->tags)}
    {$submitLabel = 'Update'}
    {$mode = 'edit'}
    {$postdate = date('d/m/Y H:i', strtotime($post->timestamp))}
{else}
    {* This must be a new post *}
    {$formAction = "/cms/posts/create/{$blog->id}/layout"}
    {$fieldTags = ''}
    {$submitLabel = 'Create'}
    {$mode = 'create'}
    {$postdate = date('d/m/Y H:i')}
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

            {include 'edit-form/teaser-summary.tpl'}
            
            <div class="field">
                <label for="fld_postcontent">Content</label>
                
                <div id="postContentEditView"></div>

                <script>
                var layouteditor = null;

                $(document).ready(function() {
                    layouteditor = new LayoutEditor({$blog->id});
                    layouteditor.setJSONElement(document.getElementById('post_content'));
                    layouteditor.setOutputElement(document.getElementById('postContentEditView'));
                    layouteditor.generateHTML();
                });
                </script>

                <textarea name="post_content" id="post_content" style="display: none;" class="post-data-field" data-key="content">{$post->content}</textarea>
            </div>
            
            {include 'edit-form/tags.tpl'}

            <input type="hidden" name="post_type" id="post_type" value="layout" class="post-data-field" data-key="type">
            
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

<div class="ui modal upload_image_modal"></div>

{include file="edit-row.tpl"}

{include file="edit-column.tpl"}


<script>
    // Get where to redirect to when the cancel
    // button is pressed. Will be different depending
    // on if the autosave has run
    var getCancelLocation = function () {
        var postID = $('#fld_postid').val();

        if (postID > 0)
            return '/cms/posts/cancelsave/' + postID;
        else {
            return '/cms/blog/overview/{$blog->id}';
        }
    };

    $('#edit_column_form .selectableimage').click(function() {
        $('#edit_column_form .selectableimage').css('border-width', '0');
        $(this).css('border', '3px solid #0c0');
        var splitUrl = $(this).attr("src").split("/");
        filename = splitUrl[splitUrl.length - 1];
        $('#edit_column_form #selected_image').val(filename);
    });
</script>

<script type="text/javascript">
var content_changed = false;

$(document).ready(function () {
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
});
</script>
</div>