{* This needs to be injected into the user view *}

<h2>Recent comments</h2>
{foreach from=$comments item=comment}
    <div class="ui segment">
        &quot;{$comment->message}&quot;
        <div class="comment-date">
            {formatdate($comment->timestamp)}
        </div>
        <div class="comment-info">
            <a href="{$comment->post()->relativeUrl()}">{$comment->post()->title}</a>
        </div>
    </div>
{foreachelse}
    <div class="ui message">
        {$user->username} has not made any comments
    </div>
{/foreach}
