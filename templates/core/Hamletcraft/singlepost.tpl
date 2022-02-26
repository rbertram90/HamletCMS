{***
 * Full-page post template 
 *
 * Variables:
 *  $post
 *  $blog
 *  $userIsContributor
 *}
 
{if $userIsContributor}
    <div class="contributor-actions">
        <a href="/cms/posts/edit/{$post->id}" class="ui basic labeled icon button"><i class="edit icon"></i>Edit</a>
        <a href="/cms/posts/delete/{$post->id}" onclick='return confirm(\"Are you sure you want to delete this post?\");' class="ui basic labeled icon button"><i class="trash icon"></i>Delete</a>
    </div>
{/if}

<article class="ui grid post full-post">

    <header class="row">
        <div class="sixteen wide column">
            <h1 class="ui header post-title">{$post->title}</h1>
            <div class="meta">
                <span class="post-date">On <a href="{$blog->relativePath()}/posts/{$post->link}">{$post->timestamp|date_format:"%d %b %Y"}</a></span>
                <span class="post-author">By <a href="{$blog->relativePath()}/author/{$post->author()->id}">{$post->author()->username}</a></span>
                
                {if count($post->tags) > 0}
                    <span class="post-tags">In 
                        {foreach $post->tags as $tag}
                            {$caption = str_replace("+", " ", $tag)}
                            <a href="{$blog->relativePath()}/tags/{$tag}">{$caption}</a>
                        {/foreach}
                    </span>
                {/if}
            
                <a href="#comments" class="leave-comment">Leave a comment</a>
            </div>
        </div>
    </header>

    {if $post->teaser_image and $post->teaser_image != "false"}
        <div class="row post-image">
            <div class="sixteen wide column">
                <img src="{$blog->resourcePath()}/images/l/{$post->teaser_image}" class="ui fluid image">
            </div>
        </div>
    {/if}

    <main class="row post-content">
        <div class="sixteen wide column">
            {$post->content}
        </div>
    </main>

    <div class="row social-icons">
        <div class="six wide column">
            <h3>Share this post</h3>
        </div>
        {$encodedTitle = rawurlencode($post->title)}
        {$encodedUrl   = rawurlencode("{$smarty.server.REQUEST_SCHEME}://{$smarty.server.SERVER_NAME}{$blog->relativePath()}/posts/{$post->link}")}
        {$unencodedUrl = "{$smarty.server.REQUEST_SCHEME}://{$smarty.server.SERVER_NAME}{$blog->relativePath()}/posts/{$post->link}"}
        <div class="ten wide column icons-column">
            <a href="https://www.facebook.com/sharer/sharer.php?u={$encodedUrl}" onclick="window.open(this.href, 'height=600,width=400'); return false;" class="ui icon facebook button"><i class="facebook icon"></i></a>
            <a href="https://twitter.com/intent/tweet?url={$encodedUrl}&text={$encodedTitle}" target="_blank" class="ui icon twitter button"><i class="twitter icon"></i></a>
            <a href="mailto:?subject={$encodedTitle}&body={$encodedUrl}" class="ui icon grey button"><i class="mail icon"></i></a>
        </div>
    </div>

    <div class="row next-prev-posts">
        {$previousPost = $post->previous()}
        {if gettype($previousPost) == "object"}
            <div class="eight wide column">
                <a href="{$blog->relativePath()}/posts/{$previousPost->link}" class="ui labeled icon fluid button">
                    <i class="left chevron icon"></i>
                    {$previousPost->title}
                </a>
            </div>
        {/if}

        {$nextPost = $post->next()}
        {if gettype($nextPost) == "object"}
            <div class="eight wide column">
                <a href="{$blog->relativePath()}/posts/{$nextPost->link}" class="ui right labeled icon fluid button">
                    <i class="right chevron icon"></i>
                    {$nextPost->title}
                </a>
            </div>
        {/if}
    </div>

    {if $post->after}
        {foreach $post->after as $afterTemplate}
            <div class="row after-content">
                <div class="sixteen wide column">
                    {include file="$afterTemplate"}
                </div>
            </div>
        {/foreach}
    {/if}

</article>
