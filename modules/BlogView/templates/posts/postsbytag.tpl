<h1>Posts tagged with {implode(" $op ", $tags)}</h1>

{if strlen($posts) == 0}
    <p class="ui message">There are no posts tagged with <i>{implode(" $op ", $tags)}</i> on this blog</p>
    {if $op == 'and' and count($tags) > 1}
        <a href="?op=or" class="ui button">Show posts that match ANY tags</a>
    {/if}
{else}
    <div class="ui buttons">
        <a href="?op=and" class="ui button{if $op == 'and'} positive{/if}">Show posts that match ALL tags</a>
        <div class="or"></div>
        <a href="?op=or" class="ui button{if $op == 'or'} positive{/if}">Show posts that match ANY tags</a>
    </div>

    <div class="ui hidden divider"></div>

    {include 'posts/multipleposts.tpl'}
{/if}