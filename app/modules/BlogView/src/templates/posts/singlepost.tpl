<article class="post">

    <header>
        {if strlen($headerDate) > 0}
            {$headerDate}
        {/if}

        <h1 class="post-title">{$post->title}</h1>

        {if $post->teaser_image and $post->teaser_image != "false"}
            <div class="teaser-image">
                <a href="{$blog_root_url}/posts/{$post->link}">
                    <img src="{$blog_file_dir}/images/{$post->teaser_image}" class="ui fluid image">
                </a>
            </div>
        {/if}
    </header>

    <main class="ui container">
        
        {* @todo create the gallery post type module! *}
        {if $post->type == 'gallery'}
            <div id='galleria_{$arrayPost['id']}'>
            $images = explode(',', $arrayPost['gallery_imagelist']);
            foreach($images as $path)
            {
                if(strlen($path) > 0)
                {
                    $gallery.= '<img src="'.$path.'" />';
                }
            }
            </div>

            <style>#galleria_{$post->id} { width: 100%; height: 400px; background: #000 }</style>
            <script>Galleria.run("#galleria_'.$arrayPost['id'].'");</script>
        {/if}
        
        {$post.trimmedContent}

    </main>
    
    <footer>
        <!-- Post Date -->
        {if strlen($footerDate) > 0}
            {$footerDate}
        {/if}
        
        <!-- Post Tags -->
        {if strlen($post->tags) > 0 and $showtags != '0'}
            <div class="post-tags">
                {foreach $post->tags as $tag}
                    {$caption = str_replace("+", " ", $tag)}
                    <a href="/blogs/{$blog->id}/tags/{$tag}" class="ui tag label">{$caption}</a>
                {/foreach}
            </div>
        {/if}

        {if $showsocialicons}
            {$encodedTitle = rawurlencode($post.title)}
            {$encodedUrl   = rawurlencode("/blog/{$blog->id}/posts/{$post->link}")}
            {$unencodedUrl = "/blog/{$blog->id}/posts/{$post->link}"}
            <div class="social-icons">
                <a href="https://www.facebook.com/sharer/sharer.php?u={$encodedUrl}" onclick="window.open(this.href, 'height=600,width=400'); return false;" class="ui icon facebook button"><i class="facebook icon"></i></a>
                <a href="https://twitter.com/intent/tweet?url={$encodedUrl}&text={$encodedTitle}" target="_blank" class="ui icon twitter button"><i class="twitter icon"></i></a>
                <a href="https://plus.google.com/share?url={$unencodedUrl}" target="_blank" class="ui icon google plus button"><i class="google plus icon"></i></a>
                <a href="mailto:?subject={$encodedTitle}&amp;body={$encodedUrl}" class="ui icon grey button"><i class="mail icon"></i></a>
            </div>
        {/if}
        
        <!-- Add / Edit Options -->
        {if $userIsContributor}
            <div class="ui buttons">
                <a href="/cms/posts/edit/{$post->id}" class="ui button">Edit</a>
                <a href="/cms/posts/delete/{$post->id}" onclick='return confirm(\"Are you sure?\");' class="ui button">Delete</a>
            </div>
            <div class="ui hidden divider"></div>
        {/if}

        <div class="ui buttons">
            {if gettype($previousPost) == "array"}
                <a href="{$blog_root_url}/posts/{$previousPost->link}" class="ui labeled icon button">
                    <i class="left chevron icon"></i>
                    Previous Post: {$previousPost->title}
                </a>
            {/if}

            {if gettype($nextPost) == "array"}
                <a href="{$blog_root_url}/posts/{$nextPost->link}" style="float:right;" class="ui right labeled icon button">
                    <i class="right chevron icon"></i>
                    Next Post: {$nextPost->title}
                </a>
            {/if}
        </div>
        <div class="ui hidden divider"></div>
    </footer>

    {if $post->after}
        {foreach $post->after as $afterTemplate}
            {include file="$afterTemplate"}
        {/foreach}
    {/if}

</article>
