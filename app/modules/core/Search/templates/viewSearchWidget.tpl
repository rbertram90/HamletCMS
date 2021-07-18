<div class="ui segment">
    {if $heading|count_characters > 0}
        <h3>{$heading}</h3>
    {/if}
    <form action="{$blog_url}/search" class="ui action left fluid input">
        <input type="text" name="q" class="" placeholder="Search ...">
        <button class="ui icon button"><i class="search icon"></i></button>
    </form>
</div>