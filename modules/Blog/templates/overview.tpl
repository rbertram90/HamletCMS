{* Blog Overview *}

<div class="ui grid">
    {if count($counts) is div by 4}
        {$columnCount = 'four'}
    {elseif count($counts) is div by 3}
        {$columnCount = 'three'}
    {elseif count($counts) is div by 2}
        {$columnCount = 'two'}
    {else}
        {$columnCount = 'one'}
    {/if}

    <div class="{$columnCount} column row">
        {foreach $counts as $name => $count}
        <div class="center aligned column">
            <div class="ui teal segment">
                <a href="/cms/posts/manage/{$blog->id}" title="Manage Posts">
                    <span class="ui header huge">{$count}</span><br><span>{$name}</span>
                </a>
            </div>
        </div>
        {/foreach}
    </div>

    <div class="stackable two column row">
        <div class="column">
            <h3 class="ui header">Latest posts</h3>
            
            {if $counts.Posts > 0}
                <div class="ui segments">
                {foreach from=$posts item=post}
                    <div class="ui segment">
                        <a href="{$blog->url()}/posts/{$post->link}">{$post->title}</a>
                        
                        {* Label for drafts *}
                        {if $post->draft == 1}<i>(draft)</i>{/if}

                        {* Label for scheduled posts *}
                        {if $post->timestamp > date('Y-m-d H:i:s')}<i>(scheduled)</i>{/if}
                        
                        <div class="comment-date">
                            {$post->timestamp|date_format}
                        </div>
                        <div class="comment-info">
                            Added by <a href="/cms/account/user/{$post->author_id}">{$post->username}</a>
                        </div>
                    </div>
                {/foreach}
                </div>
            {else}
                <p class="ui message info">Nothing has been posted on this blog, why not <a href="/cms/posts/create/{$blog->id}">make a start</a>?</p>
            {/if}
            <a href='/cms/posts/manage/{$blog->id}' class='ui teal right labeled icon right floated button'><i class="chevron right icon"></i>Manage posts</a>
            <a href='/cms/posts/create/{$blog->id}' class='ui basic teal right floated button'>New post</a>
        </div>
        {foreach from=$panels item=panel}
            <div class="column">
                {$panel}
            </div>
        {/foreach}
    </div>
    {if isset($activitylog)}
        <div class="row">
            <div class="column">
                <h3 class="ui header">Recent activity</h3>

                {if count($activitylog) > 0}
                    <div class="ui segments">
                        {foreach $activitylog as $activity}
                            <div class="ui clearing segment">
                                <img src="{$activity.user->avatar()}" class="ui left floated spaced mini circular image">
                                <a href="/cms/account/user/{$activity.user->id}">{$activity.username}</a> {$activity.text}
                                <div class="comment-info">{$activity.timestamp|date_format:"H:i jS F Y"}</div>
                            </div>
                        {/foreach}
                    </div>
                {else}
                    <p class="ui message info">No activity has been recorded for this blog</p>
                {/if}
            </div>
        </div>
    {/if}
</div>