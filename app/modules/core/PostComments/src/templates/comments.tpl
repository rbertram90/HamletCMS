{* Manage Comments *}

<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/cms/blog/overview/{$blog->id}", {$blog->name}), 'Comments')}
        </div>
    </div>

    <div class="one column row">
        <div class="column">
            {viewPageHeader('Comments', 'comment outline', {$blog->name})}
        </div>
    </div>
</div>


    <div class="ui clearing segment">
        <div class="ui buttons">
            <a href="?filter=all" class="ui {if $filter == 'all'}teal{/if} button">All</a>
            <a href="?filter=pending" class="ui {if $filter == 'pending'}teal{/if} button">Pending</a>
            <a href="?filter=approved" class="ui {if $filter == 'approved'}teal{/if} button">Approved</a>
        </div>

        <a href="/cms/comments/deleteunapproved/{$blog->id}" class="ui red icon labeled right floated button"><i class="x icon"></i> Delete all pending</a>
        <a href="/cms/comments/approveall/{$blog->id}" class="ui green icon labeled right floated button"><i class="check icon"></i> Approve all pending</a>
    </div>

{if count($comments) > 0}
    <p>Found <strong>{$comment_count}</strong> matching comments</p>

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
                {$comment->message}
            </td>
            <td>
                {$comment->timestamp|date_format}
            </td>
            <td>
                <a href="/cms/account/user/{$comment->author()->id}">{$comment->author()->fullName()}</a>
            </td>
            <td>
                <a href="{$comment->post()->relativePath()}">{$comment->post()->title}</a>
            </td>
            <td class="single line">
                {if $comment->approved == 1}
                    <div class="ui label green"><i class="icon checkmark"></i> Approved</div>
                {else}
                    <div class="ui label yellow">Pending Approval</div>
                {/if}
            </td>
            <td class="single line right aligned">
                
                {if $comment->approved == 0}
                    <button class="ui icon labeled green basic button" onclick="if(confirm('Approve this comment?')) {ldelim}window.location = '/cms/comments/approve/{$comment->id}'{rdelim}" title="Approve Comment"><i class="check icon"></i> Approve</button>
                {/if}
                <button class="ui icon labeled red basic button" onclick="if(confirm('Are you sure you wish to delete this comment?')) {ldelim}window.location = '/cms/comments/delete/{$comment->id}'{rdelim}" title="Remove Comment"><i class="x icon"></i> Remove</button>
            </td>
        </tr>
    {/strip}{/foreach}
    </table>

    <div class="ui pagination menu">
        {for $i=1 to $page_count}
            <a href="?page={$i}&filter={$filter}" class="{if $i == $current_page}active{/if} item">{$i}</a>
        {/for}
    </div>

{else}
    <p class="ui teal message">There are no comments which match your filters</p>
{/if}

