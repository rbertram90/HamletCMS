<div class="post-collection" id="post-collection-{$tag|replace:' ':'-'}">
    <h2>{$tag}</h2>
    <div class="ui cards">
    {foreach $posts as $post}
        <div class="post ui card">
            {if $post->teaser_image}
                <a class="image" href="{$blog->relativePath()}/posts/{$post->link}">
                    <img src="{$blog->resourcePath()}/images/sq/{$post->teaser_image}">
                </a>
            {/if}
            <div class="content">
                <h3 class="header"><a href="{$blog->relativePath()}/posts/{$post->link}">{$post->title}</a></h3>
                <div class="description">{$post->summary}</div>
            </div>
        </div>
    {/foreach}
    </div>
</div>