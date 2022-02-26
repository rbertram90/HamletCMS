{*
 * Default post teaser view
 *
 * Variables:
 *   - $blog
 *   - $post
 *   - $config
 *   - $userIsContributor
 *}
<div class="item post">

    {if $post->teaser_image and $post->teaser_image != "false"}
        <a href="{$blog->relativePath()}/posts/{$post->link}" class="image teaser-image">
            <img src="{$blog->resourcePath()}/images/{$post->teaser_image}" class="ui fluid image">
        </a>
    {/if}

    <div class="content">

        <h2 class="ui header">
            <a href="{$blog->relativePath()}/posts/{$post->link}">{$post->title}</a>
            <span class="sub header post-date">Posted {$post->timestamp|date_format:"%d/%m/%Y"} at {$post->timestamp|date_format:"%H:%M"}</span>
        </h2>
        
        <div class="description post-content">
            {if $post->type == 'gallery'}
                <div id="galleria_{$post->id}">
                    {foreach $post->images as $path}
                        {if strlen($path) > 0}
                            <img src="{$path}">
                        {/if}
                    {/foreach}
                </div>
                <style>#galleria_{$post->id} { width: 100%; height: 400px; background: #000; }</style>
                <script>Galleria.run("#galleria_{$post->id}");</script>
            {/if}

            {$post->summary}
        </div>
        
        <div class="extra post-footer">
            <div class="meta bottom">
                {if count($post->tags) > 0}
                    <span class="post-tags">
                        {foreach $post->tags as $tag}
                            {$caption = str_replace("+", " ", $tag)}
                            <a href="{$blog->relativePath()}/tags/{$tag}" class="ui small tag label">{$caption}</a>
                        {/foreach}
                    </span>
                {/if}
            </div>       
        </div>
    </div>
</div>