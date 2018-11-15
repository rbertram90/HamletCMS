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
    {$formAction = "/cms/posts/create/{$blog['id']}/layout"}
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

    <form action="{$formAction}" method="post" name="form_create_post" id="form_create_post" class="two column row ui form">
        
        <div class="ten wide column">
            {include 'edit-form/title.tpl'}
            
            <div class="field">
                <label for="fld_postcontent">Content</label>
                
                <div id="postContentEditView"></div>

                <script>
                var layouteditor = null;

                $(document).ready(function() {
                    layouteditor = new LayoutEditor({$blog.id});
                    layouteditor.setJSONElement(document.getElementById('post_content'));
                    layouteditor.setOutputElement(document.getElementById('postContentEditView'));
                    layouteditor.generateHTML();
                });
                </script>

                <textarea name="post_content" id="post_content" style="display: none;">{$fieldContent}</textarea>
            </div>
            
            {include 'edit-form/tags.tpl'}

            <input type="hidden" name="post_type" id="post_type" value="layout">
            
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

<div class="ui modal upload_image_modal"></div>

<div class="ui modal" id="edit_row_form">
    <div class="header">Edit row</div>
    <div class="content">
        <div class="ui message"><strong>Important</strong> reducing the number of columns will result in data being lost from the last column(s). Please ensure you have saved this content before continuing.</div>
        <form class="ui form">
            <div class="field">
                <label for="columnlayout">Layout</label>
                <select id="columnlayout" class="ui fluid dropdown">
                    <option value="singleColumn">Single column</option>
                    <option value="twoColumns_50">2 Columns: Equal widths</option>
                    <option value="twoColumns_75">2 Columns: 75% | 25%</option>
                    <option value="twoColumns_25">2 Columns: 25% | 75%</option>
                    <option value="twoColumns_66">2 Columns: 66% | 33%</option>
                    <option value="twoColumns_33">2 Columns: 33% | 66%</option>
                    <option value="threeColumns">3 Columns: Equal widths</option>
                    <option value="fourColumns">4 Columns: Equal widths</option>
                </select>
            </div>

            <input type="hidden" id="row_index" value="">
        </form>
    </div>
    <div class="actions">
        <button class="ui teal approve button">Save</button>
        <button class="ui cancel button" type='button'>Cancel</button>
    </div>
</div>

<div class="ui modal" id="edit_column_form">
    <div class="header">Edit column</div>
    <div class="content">
        <form class="ui form">
            <div class="field">
                <label for="type">Content type</label>
                <select id="type" class="ui fluid dropdown">
                    <option></option>
                    <option value="text">Text</option>
                    <option value="image">Image</option>
                </select>
            </div>

            <div class="field">
                <label>Select image:</label>
                {$imagesOutput}
                <input type="hidden" id="selected_image">
            </div>
            <div class="field">
                <label for="min_height">Minimum height</label>
                <input type="text" id="min_height" placeholder="auto">
            </div>

            <div class="field">
                <label for="text_content">Text</label>
                <textarea id="text_content"></textarea>
            </div>

            <div class="field">
                <label for="background_colour">Background colour</label>
                <select id="background_colour">
                    <option value="">None</option>
                    <option value="red">Red</option>
                    <option value="orange">Orange</option>
                    <option value="yellow">Yellow</option>
                    <option value="olive">Olive</option>
                    <option value="green">Green</option>
                    <option value="teal">Teal</option>
                    <option value="blue">Blue</option>
                    <option value="violet">Violet</option>
                    <option value="purple">Purple</option>
                    <option value="pink">Pink</option>
                    <option value="brown">Brown</option>
                    <option value="grey">Grey</option>
                    <option value="black">Black</option>
                </select>
            </div>
            <div class="field">
                <label for="font_colour">Font colour</label>
                <input type="text" id="font_colour" placeholder="#000000">
            </div>

            <input type="hidden" id="row_index" value="">
            <input type="hidden" id="column_index" value="">
        </form>
    </div>
    <div class="actions">
        <button class="ui teal approve button">Save</button>
        <button class="ui cancel button" type='button'>Cancel</button>
    </div>
</div>


<script>
    // Get where to redirect to when the cancel
    // button is pressed. Will be different depending
    // on if the autosave has run
    var getCancelLocation = function () {
        var postID = $('#fld_postid').val();

        if (postID > 0)
            return '/cms/posts/cancelsave/' + postID;
        else {
            return '/cms/blog/overview/{$blog.id}';
        }
    };

    $('#edit_column_form #type').change(function() {
        $('#edit_column_form .field').show();
        switch ($(this).val()) {
            case 'text':
                $("#selected_image").parent().hide();
            break;
            case 'image':
                $("#text_content").parent().hide();
                $("#background_colour").parent().hide();
                $("#font_colour").parent().hide();
            break;
            case '':
            $('#edit_column_form .field').hide();
            $(this).parent().show();
            break;
        }

    });

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