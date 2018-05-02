{if count($posts) == 0}
    <p class="ui message info">Nothing has been posted on this blog</p>
{/if}

{include 'blog/posts/multipleposts.tpl'}