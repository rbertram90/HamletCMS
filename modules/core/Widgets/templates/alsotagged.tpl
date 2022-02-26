{if $current_post}
    <div class="ui segment">
        <h3>{$heading}</h3>

        {foreach $posts as $tag => $relatedPosts}
            {if count($relatedPosts) > 1}
                {$tag|replace:'+':' '}
                <ul>
                {foreach $relatedPosts as $relatedPost}
                    {if $relatedPost->id != $current_post->id}
                        <li><a href="{$relatedPost->relativePath()}">{$relatedPost->title}</a></li>
                    {/if}
                {/foreach}
                </ul>
            {/if}
        {/foreach}
    </div>
{/if}