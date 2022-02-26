<div class="post ui card">
    {if $post->teaser_image}
        <a class="image" href="{$blog->relativePath()}/posts/{$post->link}">
            <img src="{$blog->resourcePath()}/images/sq/{$post->teaser_image}">
        </a>
    {/if}
    <div class="content">
        <h3 class="header"><a href="{$blog->relativePath()}/posts/{$post->link}">{$post->title}</a></h3>
        <div class="meta">
            <span class="date">{$post->timestamp|date_format:"%e %B %Y"}</span>
        </div>
        <div class="description">{$post->summary}</div>
    </div>
    <div class="extra content">
        <a href="{$blog->relativePath()}/author/{$post->author_id}"><i class="user icon"></i> {$post->author()->fullName()}</a>
    </div>
</div>