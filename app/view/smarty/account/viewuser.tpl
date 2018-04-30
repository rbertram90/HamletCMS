<div class="ui segments">
    <div class="ui clearing segment">
        <img src="/avatars/thumbs/{$user.profile_picture}" alt="Profile picture" class="ui small circular left floated image">
        <h1>{$user.name} {$user.surname} ({$user.username})</h1>
        <p>{$user.description}</p>
    </div>
    <div class="ui horizontal segments">
        <div class="ui segment">
            <strong>Birthday</strong>: {date('F jS', strtotime($user.dob))}
        </div>
        <div class="ui segment">
            <strong>Location</strong>: {$user.location}
        </div>
    </div>
</div>

<h2>Recent Comments</h2>
{foreach from=$comments item=comment}
    <div class="ui segment">
        &quot;{$comment.message}&quot;
        <div class="comment-date">
            {formatdate($comment.timestamp)}
        </div>
        <div class="comment-info">
            <a href="/blogs/{$blog.id}/posts/{$comment.link}">{$comment.title}</a>
        </div>
    </div>
{foreachelse}
    <div class="ui message">
        {$user.name} has not made any comments
    </div>
{/foreach}
