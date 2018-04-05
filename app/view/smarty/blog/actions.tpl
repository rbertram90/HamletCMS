{if $user_is_contributor}
    <a href="/cms" class="action_button">Dashboard</a>
    <a href="/cms/posts/create/{$blog.id}" class="action_button">Create New Post</a>
    <a href="/cms/posts/manage/{$blog.id}" class="action_button">Manage Posts</a>

{elseif $user_is_logged_in}
    <nav class="ui fluid vertical menu">
        <a href="/cms/blog/overview/{$blog.id}" class="item">Dashboard</a>

    {if $is_favourite}
        <a href="#" onclick="removeFavourite({$blog.id}); return false;" class="link item" title="Click to Remove from favourites list." id="btn_favourite">Added as Favourite</a>
    {else}
        <a href="#" onclick="addFavourite({$blog.id}); return false;" class="link item" title="Click to Add to favourites list." id="btn_favourite">Not in Favourites</a>
    {/if}

    </nav>
{/if}