<h3>{$heading}</h3>

{if $style == 'numbered_list'}
    {$class = 'ordered'}
{elseif $style == 'divided_list'}
    {$class = 'divided'}
{elseif $style == 'none'}
    {$class = ''}
{else}
    {$class = 'bulleted'}
{/if}

<div id="js-postlist-data" class="ui {$class} list">
    {foreach $posts as $post}
        <div class="item">
            <a href="{$blogUrl}/posts/{$post->link}">{$post->title}</a>
        </div>
    {/foreach}
</div>