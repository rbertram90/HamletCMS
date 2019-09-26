{***
 * Full-page post template 
 *
 * Variables:
 *  $post
 *  $blog
 *  $userIsContributor
 *}
<article class="ui grid post">

    <header class="row">
        <div class="sixteen wide column">
            <h1 class="ui header post-title">
                {$post->title}
                <p class="sub header">Posted by {$post->author()->fullName()} at {$post->timestamp|date_format:"%H:%M"} {$post->timestamp|date_format:"%Y/%m/%d"}</p>
            </h1>
        </div>
    </header>

    {if $post->teaser_image and $post->teaser_image != "false"}
        <div class="row">
            <div class="sixteen wide column">
                <img src="{$blog->resourcePath()}/images/xl/{$post->teaser_image}" class="ui fluid image post-image">
            </div>
        </div>
    {/if}

    <main class="row">
        <div class="sixteen wide column">
            {$post->trimmedContent}
        </div>
    </main>

    <footer class="row">
        {if count($post->tags) > 0}
            <div class="ten wide column post-tags">
                {foreach $post->tags as $tag}
                    {$caption = str_replace("+", " ", $tag)}
                    <a href="{$blog->relativePath()}/tags/{$tag}" class="ui large tag label">{$caption}</a>
                {/foreach}
            </div>
        {/if}

        {$encodedTitle = rawurlencode($post->title)}
        {$encodedUrl   = rawurlencode("{$smarty.server.REQUEST_SCHEME}://{$smarty.server.SERVER_NAME}{$blog->relativePath()}/posts/{$post->link}")}
        {$unencodedUrl = "{$smarty.server.REQUEST_SCHEME}://{$smarty.server.SERVER_NAME}{$blog->relativePath()}/posts/{$post->link}"}
        <div class="six wide right aligned column social-icons">
            <a href="https://www.facebook.com/sharer/sharer.php?u={$encodedUrl}" onclick="window.open(this.href, 'height=600,width=400'); return false;" class="ui icon facebook button"><i class="facebook icon"></i></a>
            <a href="https://twitter.com/intent/tweet?url={$encodedUrl}&text={$encodedTitle}" target="_blank" class="ui icon twitter button"><i class="twitter icon"></i></a>
            <a href="mailto:?subject={$encodedTitle}&body={$encodedUrl}" class="ui icon grey button"><i class="mail icon"></i></a>
        </div>
    </footer>

    <div class="row">
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

    {if $userIsContributor}
        <div class="row contributor-actions">
            <div class="sixteen wide column">
                <a href="/cms/posts/edit/{$post->id}" class="ui basic labeled icon button"><i class="edit icon"></i>Edit</a>
                <a href="/cms/posts/delete/{$post->id}" onclick='return confirm(\"Are you sure you want to delete this post?\");' class="ui basic labeled icon button"><i class="trash icon"></i>Delete</a>
            </div>
        </div>
    {/if}

    {if $post->after}
        {foreach $post->after as $afterTemplate}
            <div class="row">
                <div class="sixteen wide column">
                    {include file="$afterTemplate"}
                </div>
            </div>
        {/foreach}
    {/if}

</article>
