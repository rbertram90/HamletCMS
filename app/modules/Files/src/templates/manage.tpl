{* Manage Files *}
<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/cms/blog/overview/{$blog.id}", {$blog.name}), 'Files')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('Files', 'file image outline', {$blog.name})}
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
                    <p><strong>{$foldersize}</strong> KB used out of maximum <strong>{$maxfoldersize}</strong> MB</p>
                </div>
            </div>

            <button type="button" class="ui labeled icon teal button" onclick="rbrtf_showWindow('/cms/files/viewimagedrop?blogid={$blog.id}')" title="Insert Image"><i class="upload icon"></i>Add Images</button>
        </div>
    </div>
    {foreach $images as $image name=imageloop}
        {if $smarty.foreach.imageloop.index % 3 == 0}
            <div class="three column row">
        {/if}
        <div class="column">
            <div class="ui fluid card">
                <div class="blurring dimmable image">
                    <div class="ui dimmer">
                        <div class="content">
                            <div class="center">
                                <button class="ui inverted button" onclick="return confirm('Are you sure you want to delete this image?');">Delete</button>
                            </div>
                        </div>
                    </div>
                    <img src="/blogdata/{$blog.id}/images/{$image.name}">
                </div>
                <div class="content">
                    <div class="header">{$image.name}</div>
                    <div class="meta">
                        <span class="date">Uploaded on {$image.date}</span>
                    </div>
                </div>
                <div class="extra content">
                    <div><i class="image icon"></i> {$image.size} KB</div>
                </div>
            </div>
        </div>
        {if $smarty.foreach.imageloop.index % 3 == 2 or $smarty.foreach.imageloop.last}
            </div>
        {/if}
    {/foreach}
</div>

<script>
    $('.dimmable.image').dimmer({
        on:'hover'
    });
</script>