<h3>{$heading}</h3>
<div id="js-postlist-data" class="ui bulleted list">
    {foreach $posts as $post}
        <div class="item">
            <a href="{$blogUrl}/posts/{$post->link}">{$post->title}</a>
        </div>
    {/foreach}
</div>