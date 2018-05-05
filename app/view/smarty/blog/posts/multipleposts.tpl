<div class="ui divided very relaxed link items">

{foreach $posts as $post}

    <div class="item post">
    <div class="content">
        <div class="meta top">
            <span class="date">{$post.headerDate}</span>
        </div>
        
        <div class="header">
            <h2><a href="{$blog_root_url}/posts/{$post.link}">{$post.title}</a></h2>
        </div>
        
        <div class="description">
        
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
        
        <div class="meta bottom">
            <!-- Post Date -->
            <span class="post-date">{$post.footerDate}</span>
                        
            {if $shownumcomments}
                <span class="post-comment-count">{$post.numcomments} comments</span>
            {/if}
            
            <!-- Post Tags -->
            {if count($post.tags) > 0 and $showtags}
                <span class="post-tags">
                    {foreach $post.tags as $tag}
                        {$caption = str_replace("+", " ", $tag)}
                        <a href="{$blog_root_url}/tags/{$tag}" class="ui tag label">{$caption}</a>
                    {/foreach}
                </span>
            {/if}
        </div>

        <div class="ui hidden divider"></div>

        <div class="extra">
        
            <!-- Add / Edit Options -->
            {if $userIsContributor}
                <a href="/cms/posts/delete/{$post.id}" onclick="return confirm('Are you sure?');" class="ui basic right floated button">Delete</a>
                <a href="/cms/posts/edit/{$post.id}" class="ui basic right floated button">Edit</a>
            {/if}

            <!-- Social Media -->
            {if $showsocialicons}
                {$encodedTitle = rawurlencode($post.title)}
                {$encodedUrl   = rawurlencode("/blog/{$blog['id']}/posts/{$post['link']}")}
                {$unencodedUrl = "/blog/{$blog['id']}/posts/{$post['link']}"}
                <div class="social-icons">
                    <a href="https://www.facebook.com/sharer/sharer.php?u={$encodedUrl}" onclick="window.open(this.href, 'height=600,width=400'); return false;" class="ui icon facebook button"><i class="facebook icon"></i></a>
                    <a href="https://twitter.com/intent/tweet?url={$encodedUrl}&text={$encodedTitle}" target="_blank" class="ui icon twitter button"><i class="twitter icon"></i></a>
                    <a href="https://plus.google.com/share?url={$unencodedUrl}" target="_blank" class="ui icon google plus button"><i class="google plus icon"></i></a>
                    <a href="mailto:?subject={$encodedTitle}&amp;body={$encodedUrl}" class="ui icon grey button"><i class="mail icon"></i></a>
                </div>
            {/if}


        </div>
    </div>
    </div>
        
{/foreach}

</div>

{$numPages = ceil($totalnumposts / $postsperpage)}
{$paginator->showPagination($numPages, $currentPage)}