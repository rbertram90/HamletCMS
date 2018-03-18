{if $user_is_contributor}
    <a href="/" class="action_button">Dashboard</a>
    <a href="/posts/create/{$blog.id}" class="action_button">Create New Post</a>
    <a href="/posts/manage/{$blog.id}" class="action_button">Manage Posts</a>

{elseif $user_is_logged_in}
    <a href="/" class="action_button">Dashboard</a>

    {if $is_favourite}
        <a href="#" onclick="removeFavourite({$blog.id}); return false;" class="action_button btn_green" title="Click to Remove from favourites list." id="btn_favourite">Added as Favourite</a>
    {else}
        <a href="#" onclick="addFavourite({$blog.id}); return false;" class="action_button" title="Click to Add to favourites list." id="btn_favourite">Not in Favourites</a>
    {/if}

{/if}