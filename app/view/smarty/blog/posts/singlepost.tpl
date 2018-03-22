<article class="post">

    <header>
        {if strlen($headerDate) > 0}
            {$headerDate}
        {/if}

        <h1 class="post-title">{$post.title}</h1>
    </header>

    <!-- Note we won't always want to use wiki language! need to either ask user or 
    programmatically determine if the post uses wiki -->
    <main class="ui text container">

        {if $post.type == 'video'}
            {if $post.videosource == 'youtube'}
                <iframe width="100%" style="max-width:560px;" height="315" src="//www.youtube.com/embed/{$post.videoid}" frameborder="0" allowfullscreen></iframe>

            {elseif $post.videosource == 'vimeo'}
                <iframe src="//player.vimeo.com/video/{$post.videoid}?title=0&amp;byline=0&amp;portrait=0&amp;color=fafafa" width="560" height="315" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
            {/if}
        {/if}
        
        {if $post.type == 'gallery'}
        
            $gallery = "<div id='galleria_{$arrayPost['id']}'>";
            
            $images = explode(',', $arrayPost['gallery_imagelist']);
            
            foreach($images as $path)
            {
                if(strlen($path) > 0)
                {
                    $gallery.= '<img src="'.$path.'" />';
                }
            }
            
            $gallery.= '</div>';
            $gallery.= '<style>#galleria_'.$arrayPost['id'].'{ width: 100%; height: 400px; background: #000 }</style>';
            $gallery.= '<script>Galleria.run("#galleria_'.$arrayPost['id'].'");</script>';
            
            echo $gallery;
        {/if}
        
        {$mdContent}

    </main>
    
    <footer>
        <!-- Post Date -->
        {if strlen($footerDate) > 0}
            {$footerDate}
        {/if}
        
        <!-- Post Tags -->
        {if strlen($post.tags) > 0 and $showtags != '0'}
            <div class="post-tags">
                {foreach $post.tags as $tag}
                    {$caption = str_replace("+", " ", $tag)}
                    <a href="/blogs/{$blog.id}/tags/{$tag}" class="ui tag label">{$caption}</a>
                {/foreach}
            </div>
        {/if}

        {if $showsocialicons}
            {$encodedTitle = rawurlencode($post.title)}
            {$encodedUrl   = rawurlencode("/blog/{$blog['id']}/posts/{$post['link']}")}
            {$unencodedUrl = "/blog/{$blog['id']}/posts/{$post['link']}"}
            <style>.social-icons img { width:30px; height:30px; }</style>
            <div class="social-icons">
                <a href="https://www.facebook.com/sharer/sharer.php?u={$encodedUrl}" onclick="window.open(this.href, 'height=600,width=400'); return false;"><img src="/resources/icons/social/facebook256.png" /></a>
                <a href="https://twitter.com/intent/tweet?url={$encodedUrl}&text={$encodedTitle}" target="_blank"><img src="/resources/icons/social/twitter256.png" /></a>
                <a href="https://plus.google.com/share?url={$unencodedUrl}" target="_blank"><img src="/resources/icons/social/googleplus256.png" /></a>
                <a href="mailto:?subject={$encodedTitle}&amp;body={$encodedUrl}"><img src="/resources/icons/social/email256.png" /></a>
            </div>
            <div class="ui hidden divider"></div>
        {/if}
        
        <!-- Add / Edit Options -->
        {if $userIsContributor}
            <div class="ui buttons">
                <a href="/posts/edit/{$post.id}" class="ui button">Edit</a>
                <a href="/posts/delete/{$post.id}" onclick='return confirm(\"Are you sure?\");' class="ui button">Delete</a>
            </div>
            <div class="ui hidden divider"></div>
        {/if}

        <div class="ui buttons">
            {if gettype($previousPost) == "array"}
                <a href="/blogs/{$post.blog_id}/posts/{$previousPost.link}" class="ui labeled icon button">
                    <i class="left chevron icon"></i>
                    Previous Post: {$previousPost.title}
                </a>
            {/if}

            {if gettype($nextPost) == "array"}
                <a href="/blogs/{$post.blog_id}/posts/{$nextPost.link}" style="float:right;" class="ui right labeled icon button">
                    <i class="right chevron icon"></i>
                    Next Post: {$nextPost.title}
                </a>
            {/if}
        </div>
        <div class="ui hidden divider"></div>
    </footer>

    <div>
        {include file='blog/comments/postcomments.tpl'}
    </div>

    <div class="ui hidden divider"></div>

    <div>
        {include file='blog/comments/newcommentform.tpl'}
    </div>

</article>