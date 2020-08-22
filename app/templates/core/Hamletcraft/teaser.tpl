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

    <div class="content">

        <h2 class="ui header">
            <a href="{$blog->relativePath()}/posts/{$post->link}">{$post->title}</a>
        </h2>
        
        <div class="meta">
            <span class="post-date">On <a href="{$blog->relativePath()}/posts/{$post->link}">{$post->timestamp|date_format:"%d %b %Y"}</a></span>
            <span class="post-author">By <a href="/cms/account/user/{$post->author()->id}">{$post->author()->username}</a></span>
            
            {if count($post->tags) > 0}
                <span class="post-tags">In 
                    {foreach $post->tags as $tag}
                        {$caption = str_replace("+", " ", $tag)}
                        <a href="{$blog->relativePath()}/tags/{$tag}">{$caption}</a>
                    {/foreach}
                </span>
            {/if}
        
            <a href="{$blog->relativePath()}/posts/{$post->link}#comments" class="leave-comment">Leave a comment</a>
        </div>
        
        {if $post->teaser_image and $post->teaser_image != "false"}
            <a href="{$blog->relativePath()}/posts/{$post->link}" class="image teaser-image">
                <img src="{$blog->resourcePath()}/images/m/{$post->teaser_image}" class="ui fluid image">
            </a>
        {/if}
        
        <div class="description post-content">
            {$post->content}
        </div>
        
        <a href="{$blog->relativePath()}/posts/{$post->link}" class="read-more-link">Read more</a>
        
    </div>
</div>