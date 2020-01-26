<div class="post-comments-list">
    <h2>Comments</h2>

    <div class="ui comments">
    {foreach $comments as $comment}
        <div class="comment">
            <a class="avatar">
                <img src="/avatars/{$comment->author()->profile_picture}">
            </a>
            <div class="content">
                <a href="/cms/account/user/{$comment->user_id}" class="author">{$comment->author()->fullName()} ({$comment->author()->username})</a>
                <div class="metadata">
                    <div class="date">{$comment->timestamp|date_format:"%l:%M %p, %e %b %Y"}</div>
                </div>
                <div class="text">{$comment->message}</div>
            </div>
        </div>
    {foreachelse}
        <p class="ui message info">No comments have been made on this post</p>
    {/foreach}
    </div>
</div>