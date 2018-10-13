{* Blog Overview *}

<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array(), $blog['name'])}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader("{$blog.name}", 'book')}
        </div>
    </div>

    <div class="four column row">
        <div class="center aligned column">
            <div class="ui teal segment">
                <a href="/cms/posts/manage/{$blog.id}" title="Manage Posts">
                    <span class="ui header huge">{$counts.posts}</span><br><span>Posts</span>
                </a>
            </div>
        </div>
        <div class="center aligned column">
            <div class="ui teal segment">
                <a href="/cms/comments/all/{$blog.id}" title="View Comments">
                    <span class="ui header huge">{$counts.comments}</span><br><span>Comments</span>
                </a>
            </div>
        </div>
        <div class="center aligned column">
            <div class="ui teal segment">
                <a href="/cms/contributors/manage/{$blog.id}" title="Manage Contributors">
                    <span class="ui header huge">{$counts.contributors}</span><br><span>Contributors</span>
                </a>
            </div>
        </div>
        <div class="center aligned column">
            <div class="ui teal segment">
                <a href="/cms/posts/manage/{$blog.id}" title="Manage Posts">
                    <span class="ui header huge">{$counts.totalviews}</span><br><span>Total Post Views</span>
                </a>
            </div>
        </div>
    </div>

    <div class="stackable two column row">
        <div class="column">
            <h3 class="ui header">Latest Posts</h3>
            
            {if $counts.posts > 0}
                <div class="ui segments">
                {foreach from=$posts item=post}
                    <div class="ui segment">
                        <a href='/blogs/{$blog.id}/posts/{$post['link']}'>{$post.title}</a>
                        
                        {* Label for drafts *}
                        {if $post.draft == 1}<i>(draft)</i>{/if}

                        {* Label for scheduled posts *}
                        {if $post.timestamp > date('Y-m-d H:i:s')}<i>(scheduled)</i>{/if}
                        
                        <div class="comment-date">
                            {formatDate($post.timestamp)}
                        </div>
                        <div class="comment-info">
                            Added by <a href="/cms/account/user/{$post.author_id}">{$post.username}</a>
                        </div>
                    </div>
                {/foreach}
                </div>
            {else}
                <p class="ui message info">Nothing has been posted on this blog, why not <a href="/cms/posts/create/{$blog.id}">make a start</a>?</p>
            {/if}
            <a href='/cms/posts/manage/{$blog.id}' class='ui teal right floated button'>Manage Posts &gt;</a>
            <a href='/cms/posts/create/{$blog.id}' class='ui basic teal right floated button'>New Post &gt;</a>
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
            <div class="ui segments">
            {foreach $activitylog as $activity}
                <div class="ui segment">
                    <a href="/cms/account/user/{$activity.user_id}">{$activity.username}</a> {$activity.text}
                    <div class="comment-info">{$activity.timestamp|date_format:"H:i jS F Y"}</div>
                </div>
            {foreachelse}
                <p>No activity has been recorded for this blog</p>
            {/foreach}
            </div>
        </div>
    </div> 
</div>