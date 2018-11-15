<h3 class="ui header">Recent Comments</h3>
{foreach $comments as $comment}
    <div class="ui segment">
        &quot;{$comment.message}&quot;
        <div class="comment-date">
            {$comment.timestamp|date_format}
        </div>
        <div class="comment-info">
            {if $comment.user_id == $currentUser.id}
                Added by <a href="/cms/account/user/{$comment.user_id}">You</a> on <a href="/blogs/{$blog.id}/posts/{$comment.link}">{$comment.title}</a>
            {else}
                Added by <a href="/cms/account/user/{$comment.user_id}">{$comment.name}</a> on <a href="/blogs/{$blog.id}/posts/{$comment.link}">{$comment.title}</a>
            {/if}
        </div>
    </div>
{foreachelse}
    <p class="ui message info">No comments have been made on your posts on this blog :(</p>
{/foreach}
<a href='/cms/comments/all/{$blog.id}' class='ui right labeled icon teal right floated button'><i class="chevron right icon"></i>All Comments</a>