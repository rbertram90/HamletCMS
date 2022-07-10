<div class="ui segment">
    <h3>{$heading}</h3>
    <div id="js-taglist-data" class="ui {if $display == 'list'}bulleted list{else}labels{/if}">
        {foreach $tags as $tag}
            {if isset($currentTags)}
                {if in_array($tag.slug, $currentTagsList)}
                    {$slug = $currentTagsList|except:$tag.slug}
                {else}
                    {$slug = "{$tag.text},$currentTags"}
                {/if}
            {else}
                {$slug = $tag.text}
            {/if}
            
            {if $display == 'list'}
                <div class="item">
                    <a href="{$blog->relativePath()}/tags/{$slug}?op={$op}">
                        {if isset($currentTagsList) && in_array($tag.slug, $currentTagsList)}
                            <strong>{$tag.text}</strong>
                        {else}
                            {$tag.text}
                        {/if}
                    </a>
                </div>
            {else}
                <a href="{$blog->relativePath()}/tags/{$slug}?op={$op}" class="ui label{if isset($currentTagsList) && in_array($tag.slug, $currentTagsList)} green{/if}">
                    {$tag.text}
                    <div class="detail">{$tag.count}</div>
                </a>
            {/if}
        {/foreach}
    </div>
</div>