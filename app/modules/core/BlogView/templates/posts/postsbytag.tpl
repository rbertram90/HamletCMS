<h1>Posts tagged with '{$tagName}'</h1>

{if strlen($posts) == 0}
    <p class="info">There are no posts tagged with <i>'{$tagName}'</i> on this blog</p>
{/if}
    
{include 'posts/multipleposts.tpl'}