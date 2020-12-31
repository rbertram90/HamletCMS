{if $user_is_logged_in}
    <div class="ui fluid vertical menu actions">
        <a href="/cms/blog/overview/{$blog->id}" class="item">Dashboard</a>
        {foreach $blog_actions_menu->getLinks() as $link}
          <a href="{$link->url}" class="item">{$link->text}</a>
        {/foreach}
    </div>
{/if}
