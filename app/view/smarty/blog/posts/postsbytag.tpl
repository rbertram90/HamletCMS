<h1>Posts tagged with '{$tagName}'</h1>

{if count($posts) == 0}
    <p class="info">There are no posts tagged with <i>'{$tagName}'</i> on this blog</p>
{/if}
    
{foreach $posts as $post}

    <div class="post">
        {$post.headerDate}
        
        <h3 class="post-title"><a href="/blog/{$blog.id}/posts/{$post.link}">{$post.title}</a></h3>
        
        <div class="post-content">
        
            {if $post.type == 'video'}
                {if $post.videosource == 'youtube'}
                    <iframe width="100%" height="400" src="//www.youtube.com/embed/{$post.videoid}" frameborder="0" allowfullscreen></iframe>

                {elseif $post.videosource == 'vimeo'}
                    <iframe src="//player.vimeo.com/video/{$post.videoid}?title=0&amp;byline=0&amp;portrait=0&amp;color=fafafa" width="560" height="315" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
                {/if}
            {/if}
                
            {if $post.type == 'gallery'}
                <div id="galleria_{$post.id}">
                    {foreach $post.images as $path}
                        {if strlen($path) > 0}
                            <img src="{$path}">
                        {/if}
                    {/foreach}
                </div>
                <style>#galleria_{$post.id} { width: 100%; height: 400px; background: #000; }</style>
                <script>Galleria.run("#galleria_{$post.id}");</script>
            {/if}

            {$post.trimmedContent}
        
        </div>
        
        <div class="post-footer">
            <!-- Post Date -->
            {$post.footerDate}
            
            <!-- Post Tags -->
            {if count($post.tags) > 0 and $showtags}
                <p class="post-tags">Tags: 
                    {foreach $post.tags as $tag}
                        {$caption = str_replace("+", " ", $tag)}
                        <a href="/blogs/{$blog.id}/tags/{$tag}" class="tag">{$caption}</a>
                    {/foreach}
                </p>
            {/if}
            
            {if $shownumcomments}
                <p class="post-comment-count">{$post.numcomments} comments</p>
            {/if}
            
            <!-- Add / Edit Options -->
            {if $userIsContributor}
                <a href="/posts/edit/{$post.id}">Edit</a> | 
                <a href="/posts/delete/{$post.id}" onclick='return confirm(\"Are you sure?\");'>Delete</a>
            {/if}
            
            <!-- Social Media -->
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
            {/if}
        </div>
    </div>
        
{/foreach}

{$numPages = ceil($totalnumposts / $postsperpage)}
{$paginator->showPagination($numPages, $currentPage)}