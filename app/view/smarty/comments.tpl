{* Manage Comments *}

<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/overview/{$blog.id}", {$blog.name}), 'Comments')}
        </div>
    </div>

    <div class="one column row">
        <div class="column">
            {viewPageHeader('Comments', 'comment.png', {$blog.name})}
        </div>
    </div>
</div>

<p class="info">Total Comments: {count($comments)}</p>

<style>
    .comments-column {ldelim}
        width:50%;
        display:inline-block;
        vertical-align:top;
    {rdelim}
</style>

<div class="comments-column">
{* Output First Column *}
{foreach $comments as $comment}{strip}

    {if $comment@iteration is even}

        <div class="contributor-card" style="height:auto; width:98%;">
            <img src="/resources/icons/64/cross.png" onclick="if(confirm('Are you sure you wish to delete this comment?')) {ldelim}window.location = '/comments/{$comment.blog_id}/delete/{$comment.id}'{rdelim}" title="Remove Comment" style="height:20px; cursor:pointer; float:right;" />

            <p style="color:#777;">{formatdate($comment.timestamp)}</p>
            <p style="font-size:1.1em;">&#147;{$comment.message}&#148;<a href="/account/user/{$comment.user_id}" style="font-size:0.8em;"> - {$comment.user.username}</a></p>

            <p style="font-size:0.75em;">On post <a href="/blogs/{$comment.blog_id}/posts/{$comment.link}">{$comment.title}</a></p>
        </div>

    {/if}

{/strip}{foreachelse}
    <p class="info">No comments have been made on your posts</p>

{/foreach}
    
</div><div class="comments-column">

{* Output Second Column *}
{foreach $comments as $comment}{strip}
    
    {if $comment@iteration is odd}

        <div class="contributor-card" style="height:auto; width:98%;">
            <img src="/resources/icons/64/cross.png" onclick="if(confirm('Are you sure you wish to delete this comment?')) {ldelim}window.location = '/comments/{$comment.blog_id}/delete/{$comment.id}'{rdelim}" title="Remove Comment" style="height:20px; cursor:pointer; float:right;" />

            <p style="color:#777;">{formatdate($comment.timestamp)}</p>
            <p style="font-size:1.1em;">&#147;{$comment.message}&#148;<a href="/account/user/{$comment.user_id}" style="font-size:0.8em;"> - {$comment.user.username}</a></p>

            <p style="font-size:0.75em;">On post <a href="/blogs/{$comment.blog_id}/posts/{$comment.link}">{$comment.title}</a></p>
        </div>

    {/if}
    
{/strip}{/foreach}
</div>


<div class="push-right" style="margin-top:50px;">
    <a href="/posts/{$blog.id}/new" class="action_button">New Post</a>
    <a href="/posts/{$blog.id}" class="action_button">Current Posts</a>
    <a href="/config/{$blog.id}" class="action_button">Manage Settings</a>
    <a href="/contributors/{$blog.id}" class="action_button">Contributors</a>
    <a href="/blogs/{$blog.id}" target="_blank" class="action_button btn_red">View Blog</a>
</div>