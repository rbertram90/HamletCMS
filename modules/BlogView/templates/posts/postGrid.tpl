<div class="post-collection" id="post-collection-{$tag|replace:' ':'-'}">
    <h2>{$tag}</h2>
    <div class="ui three stackable cards">
    {foreach $posts as $post}
        {include file="$cardTemplate"}
    {/foreach}
    </div>
    <div class="tag-cta">
        <a href="{$blog->relativePath()}/tags/{$tag}">View more posts tagged with <span class="tag-name">{$tag}</span></a>
    </div>
</div>