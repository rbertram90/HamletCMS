{* Manage Files *}
<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/blog/overview/{$blog.id}", {$blog.name}), 'Files')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('Files', 'landscape.png', {$blog.name})}
        </div>
    </div>
</div>
{if count($images) == 0}
    <p class="ui message info">No images have been uploaded to this blog</p>
{/if}

<style>
    .imageholder {
        width:31%;
        height:120px;
        display:inline-block;
        background-color:#fff;
        margin:1%;
    }
    .imageholder .image {
        background-size:cover;
        width:100%;
        height:100%;
        text-align:center;
        padding-top:80px;
    }
    .imageholder .image button {
        display:none;
    }
    .imageholder .image:hover button {
        display:inline;
    }
    .imageholder p {
        padding:2px;
        margin:0px;
        font-size:0.9em;
    }
</style>

<div>
    <button type="button" onclick="rbrtf_showWindow('/files/viewimagedrop?blogid={$blog.id}')" title="Insert Image"><img src="/resources/icons/document_image_add_32.png" style="width:15px; height:15px;" /> Add Image</button>
    
    <p>Total Space Used = {$foldersize} KB <br> Limit = 50 MB</p>
</div>

<div style="vertical-align:top;">
    {foreach $images as $image}{strip}
    <div class="imageholder">
        <div style="background-image:url('/blogdata/{$blog.id}/images/{$image.name}');" class="image">
            <form action="/files/delete/{$blog.id}/{$image.file}" method="POST">
                <button onclick="return confirm('Are you sure you want to delete this image?');">Delete</button>
            </form>
        </div>
        <p style="border-bottom:1px solid #ccc;">File size: {$image.size} KB</p>
        <p>Uploaded: {$image.date}</p>
    </div>
    {/strip}{/foreach}
</div>