{if $user_is_contributor}
    <div class="ui fluid vertical menu actions">
        <a href="/cms/blog/overview/{$blog->id}" class="item">Dashboard</a>
        <a href="/cms/posts/create/{$blog->id}" class="item">Create New Post</a>
        <a href="/cms/posts/manage/{$blog->id}" class="item">Manage Posts</a>
    </div>

{elseif $user_is_logged_in}
    <div class="ui fluid vertical menu actions">
        <a href="/cms/blog/overview/{$blog->id}" class="item">Dashboard</a>
        {if $is_favourite}
            <a href="#" onclick="removeFavourite({$blog->id}); return false;" class="link item" title="Click to Remove from favourites list." id="btn_favourite">Added as Favourite</a>
        {else}
            <a href="#" onclick="addFavourite({$blog->id}); return false;" class="link item" title="Click to Add to favourites list." id="btn_favourite">Not in Favourites</a>
        {/if}
    </div>
{/if}