<div class="ui segments widget">
    <div class="ui segment">
        <h3>{$heading}</h3>

        {if $display == 'list'}
            <div id="js-taglist-data" class="ui bulleted list">
                {foreach $tags as $tag}
                    <div class="item"><a href="/blogs/{$blog->id}/tags/{$tag.text}">{$tag.text}</a></div>
                {/foreach}
            </div>
        {else}
            <div id="js-taglist-data" class="ui labels">
                {foreach $tags as $tag}
                    <a href="/blogs/{$blog->id}/tags/{$tag.text}" class="ui label">
                        {$tag.text}
                        <div class="detail">{$tag.count}</div>
                    </a>
                {/foreach}
            </div>
        {/if}
    </div>
</div>