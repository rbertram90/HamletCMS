{* Blog Overview *}

<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array(), $blog->name)}
        </div>
    </div>
    <div class="one column row">
        <div class="column">

        <h1 class="ui header cms-page-header">
            {if $blog->logo}
                <img src="/blogdata/{$blog->id}/{$blog->icon}" alt="Blog logo">
            {else}
                <i class="book icon"></i>
            {/if}
            <div class="content">
                {$blog->name}
            </div>
        </h1>
        </div>
    </div>

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
            <h3 class="ui header">Latest Posts</h3>
            
            {if $counts.posts > 0}
                <div class="ui segments">
                {foreach from=$posts item=post}
                    <div class="ui segment">
                        <a href='/blogs/{$blog->id}/posts/{$post->link}'>{$post->title}</a>
                        
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
            <a href='/cms/posts/manage/{$blog->id}' class='ui teal right labeled icon right floated button'><i class="chevron right icon"></i>Manage Posts</a>
            <a href='/cms/posts/create/{$blog->id}' class='ui basic teal right floated button'>New Post</a>
        </div>
        {foreach from=$panels item=panel}
            <div class="column">
                {$panel}
            </div>
        {/foreach}
    </div>
    <div class="row">
        <div class="column">
            <h3 class="ui header">Recent activity</h3>

            {if count($activitylog) > 0}
                <div class="ui segments">
                    {foreach $activitylog as $activity}
                        <div class="ui segment">
                            <a href="/cms/account/user/{$activity.user_id}">{$activity.username}</a> {$activity.text}
                            <div class="comment-info">{$activity.timestamp|date_format:"H:i jS F Y"}</div>
                        </div>
                    {/foreach}
                </div>
            {else}
                <p class="ui message info">No activity has been recorded for this blog</p>
            {/if}
        </div>
    </div> 
</div>