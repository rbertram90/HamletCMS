{if $heading}
    <h3>{$heading}</h3>
{/if}

<div class="ui {$orientation} {$colour} menu">
    {foreach $menu->items() as $link}
        {if $link->new_window}
            <a href="{$link->url()}" class="item" target="_blank">{$link->text}</a>
        {else}
            <a href="{$link->url()}" class="item">{$link->text}</a>
        {/if}
    {/foreach}
</div>