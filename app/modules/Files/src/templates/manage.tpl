{* Manage Files *}
<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/cms/blog/overview/{$blog->id}", {$blog->name}), 'Files')}
            {viewPageHeader('Files', 'file image outline', {$blog->name})}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {if count($images) == 0}
                <p class="ui message info">No images have been uploaded to this blog</p>
            {/if}

            <div class="ui visible teal icon message">
                <i class="folder open outline icon"></i>
                <div class="content">
                    <p><strong>{$foldersize}</strong> MB used out of maximum <strong>{$maxfoldersize}</strong> MB</p>
                </div>
            </div>

            <button type="button" class="ui labeled icon teal button" title="Add images" id="add_images_button"><i class="upload icon"></i>Add images</button>
        </div>
    </div>
    {foreach $images as $image name=imageloop}
        {if $smarty.foreach.imageloop.index % 3 == 0}
            <div class="three column row">
        {/if}
        {$imageData = getimagesize("{$smarty.const.SERVER_PATH_BLOGS}/{$blog->id}/images/{$image.name}")}

        <div class="column">
            <div class="ui fluid card">
                <div class="blurring dimmable image">
                    <div class="ui dimmer">
                        <div class="content">
                            <div class="center">
                                <form action="/cms/files/delete/{$blog->id}/{$image.file}">
                                    <a href="/blogdata/{$blog->id}/images/{$image.name}" class="ui inverted button" target="_blank">Open</a>
                                    <button class="ui inverted button" onclick="return confirm('Are you sure you want to delete this image?');">Delete</button>
                                </form>
                            </div>
                        </div>
                    </div>
                    <img src="/blogdata/{$blog->id}/images/{$image.name}">
                </div>
                <div class="content">
                    <div class="header">{$image.name}</div>
                    <div class="meta">
                        <span class="date">Uploaded on {$image.date}</span>
                    </div>
                </div>
                <div class="extra content">
                    <div class="right floated"><i class="image icon"></i> {$imageData.0} x {$imageData.1}</div>
                    <div><i class="image icon"></i> {$image.size} KB</div>
                </div>
            </div>
        </div>
        {if $smarty.foreach.imageloop.index % 3 == 2 or $smarty.foreach.imageloop.last}
            </div>
        {/if}
    {/foreach}
</div>

<div class="ui modal" id="image_upload_modal">
    <i class="close icon"></i>
    <div class="ui header">
        <i class="upload icon"></i>
        <div class="content">Upload Images</div>
    </div>
    <div class="content main">
        <img src="/images/ajax-loader.gif" alt="Loading..." />
    </div>
    <div class="actions">
        <button id="finishButton" class="ui labeled icon teal button" onclick="window.location.reload();"><i class="check icon"></i>Finish</button>
    </div>
</div>

<script>
    $('#add_images_button').click(function() {
        $('#image_upload_modal .main').load('/cms/files/viewimagedrop?blogid={$blog->id}');
        $('#image_upload_modal').modal('show');
    });

    $('.dimmable.image').dimmer({
        on:'hover'
    });
</script>