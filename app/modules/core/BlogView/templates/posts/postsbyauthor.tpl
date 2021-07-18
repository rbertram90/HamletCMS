<h1>Posts created by '{$authorName}'</h1>

{if count($posts) == 0}
    <p class="info">There are no posts created by <i>'{$authorName}'</i> on this blog</p>
{/if}
    
{include 'posts/multipleposts.tpl'}