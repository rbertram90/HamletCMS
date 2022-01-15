<div class="field">
    <label for="teaser_image">Teaser image</label>
    <style>#teaser_image_image img { max-height: 200px; max-width: 200px; }</style>
    <div id="teaser_image_image">
    {if isset($post) && $post->teaser_image != ''}
        <img src="/blogdata/{$blog->id}/images/{$post->teaser_image}">
    {/if}
    </div>
    <button type="button" id="teaser_image_select" title="Select Image" class="ui icon button" data-no-spinner="true">
        <i class="camera icon"></i>
    </button>
    <button type="button" id="remove_teaser_image" class="ui icon button" data-no-spinner="true">
        <i class="remove icon"></i>
    </button>
    <input type="hidden" name="teaser_image" value="{if isset($post)}{$post->teaser_image}{/if}" class="post-data-field" data-key="teaserImage">
</div>

<script>
$(document).ready(function() {
    $("body").append("<div class='ui modal upload_image_modal'></div>");

    $('#teaser_image_select').click(function() {
        $('.ui.upload_image_modal').load('/cms/files/fileselect/{$blog->id}', { 'csrf_token': CSRFTOKEN, 'elemid': 'teaser_image_image', 'format': 'html', 'replace': 1 }, function() {
            $(this).modal('show');
        });
    });

    $("#remove_teaser_image").click(function() {
        $("#teaser_image_image").html('');
        $("input[name='teaser_image']").val('');
    });
});
</script>