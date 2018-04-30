{* Manage Comments *}

<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/cms/blog/overview/{$blog.id}", {$blog.name}), 'Comments')}
        </div>
    </div>

    <div class="one column row">
        <div class="column">
            {viewPageHeader('Comments', 'comment.png', {$blog.name})}
        </div>
    </div>
</div>

<p class="info">Total Comments: <strong>{count($comments)}</strong></p>

{if count($comments) == 0}
    <div class="segment">No comments have been made on your posts</div>
{else}
    <table class="ui table">
        <thead>
            <tr>
                <th>Comment Text</th>
                <th>Date</th>
                <th>User</th>
                <th>Post</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
    {foreach $comments as $comment}{strip}
        <tr>
            <td>
                {$comment.message}
            </td>
            <td>
                {formatdate($comment.timestamp)}
            </td>
            <td>
                <a href="/cms/account/user/{$comment.userid}">{$comment.username}</a>
            </td>
            <td>
                <a href="/blogs/{$comment.blog_id}/posts/{$comment.link}">{$comment.title}</a>
            </td>
            <td class="single line">
                {if $comment['approved'] == 1}
                    <div class="ui label green"><i class="icon checkmark"></i> Approved</div>
                {else}
                    <div class="ui label yellow">Pending Approval</div>
                {/if}
            </td>
            <td class="single line right aligned">
                
                {if $comment['approved'] == 0}
                    <button class="ui green button" onclick="if(confirm('Approve this comment?')) {ldelim}window.location = '/cms/comments/approve/{$comment.id}'{rdelim}" title="Approve Comment">Approve</button>
                {/if}
                <button class="ui button" onclick="if(confirm('Are you sure you wish to delete this comment?')) {ldelim}window.location = '/cms/comments/delete/{$comment.id}'{rdelim}" title="Remove Comment">Delete</button>
            </td>
        </tr>
    {/strip}{/foreach}
    </table>
{/if}
