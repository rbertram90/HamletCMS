<div class="post-comments-list">
    <h2>Comments</h2>

    <div class="ui comments">
    {foreach $comments as $comment}
        {include file="$commentTemplatePath"}
    {foreachelse}
        <p class="ui message info">No comments have been made on this post</p>
    {/foreach}
    </div>
</div>