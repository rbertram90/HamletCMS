<div class="comments">
    <h2>Comments</h2>
    
    {if count($comments) == 0}
        <p class="ui message info">No comments have been made on this post</p>
    {/if}
    
    {foreach $comments as $comment}
        {* $user = $modelUers->getUserById($comment['user_id']); *}
        <p><b>{$comment->name} ({$comment->fullname})</b></p>
        <p><i>&quot;{$comment->message}&quot;</i></p>
    {/foreach}
    
</div>