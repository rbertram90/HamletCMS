{*
 * Default post teaser view
 *
 * Variables:
 *   - $post
 *   - $config
 *   - $blog_root_url
 *   - $blog_file_dir
 *   - $shownumcomments
 *   - $showtags
 *   - $showsocialicons
 *   - $userIsContributor
 *}
<div class="item post">

    {if $post->teaser_image and $post->teaser_image != "false"}
        <div class="image">
            <div class="teaser-image">
                <a href="{$blog_root_url}/posts/{$post->link}">
                    <img src="{$blog_file_dir}/images/{$post->teaser_image}" class="ui fluid image">
                </a>
            </div>
        </div>
    {/if}

    <div class="content">

        <h2 class="header"><a href="{$blog_root_url}/posts/{$post->link}">{$post->title}</a></h2>
        
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

            {$post->trimmedContent}
        </div>
        
        <div class="extra post-footer">
            <div class="meta bottom">
                <!-- Post Date -->
                <span class="post-date">Posted {$post->timestamp|date_format:"%Y/%m/%d"} at {$post->timestamp|date_format:"%H:%M"}</span>
                
                {if $shownumcomments}
                    <span class="post-comment-count">{$post->numcomments} comments</span>
                {/if}
                
                <!-- Post Tags -->
                {if count($post->tags) > 0 && $showtags}
                    <span class="post-tags">
                        {foreach $post->tags as $tag}
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
                    <a href="/cms/posts/delete/{$post->id}" onclick="return confirm('Are you sure?');" class="ui basic right floated button">Delete</a>
                    <a href="/cms/posts/edit/{$post->id}" class="ui basic right floated button">Edit</a>
                {/if}

                <!-- Social Media -->
                {if $showsocialicons}
                    {$encodedTitle = rawurlencode($post->title)}
                    {$unencodedUrl = "{$smarty.server.REQUEST_SCHEME}://{$smarty.server.SERVER_NAME}{$blog_root_url}/posts/{$post->link}"}
                    {$encodedUrl   = rawurlencode($unencodedUrl)}

                    <div class="social-icons">
                        <a href="https://www.facebook.com/sharer/sharer.php?u={$encodedUrl}" onclick="window.open(this.href, 'height=600,width=400'); return false;" class="ui icon facebook button"><i class="facebook icon"></i></a>
                        <a href="https://twitter.com/intent/tweet?url={$encodedUrl}&text={$encodedTitle}" target="_blank" class="ui icon twitter button"><i class="twitter icon"></i></a>
                        <a href="mailto:?subject={$encodedTitle}&amp;body={$encodedUrl}" class="ui icon grey button"><i class="mail icon"></i></a>
                    </div>
                {/if}
            </div>
        </div>
    </div>
</div>