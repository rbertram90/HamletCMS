{* This needs to be injected into the user view *}

<h2>Recent Comments</h2>
{foreach from=$comments item=comment}
    <div class="ui segment">
        &quot;{$comment->message}&quot;
        <div class="comment-date">
            {formatdate($comment->timestamp)}
        </div>
        <div class="comment-info">
            <a href="/blogs/{$blog->id}/posts/{$comment->link}">{$comment->title}</a>
        </div>
    </div>
{foreachelse}
    <div class="ui message">
        {$user.name} has not made any comments
    </div>
{/foreach}
