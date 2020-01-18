<div class="comments">
    <h2>Comments</h2>

    {foreach $comments as $comment}
        <p><b>{$comment->author()->fullName()} ({$comment->author()->username})</b></p>
        <p><i>&quot;{$comment->message}&quot;</i></p>
    {foreachelse}
        <p class="ui message info">No comments have been made on this post</p>
    {/foreach}
    
</div>