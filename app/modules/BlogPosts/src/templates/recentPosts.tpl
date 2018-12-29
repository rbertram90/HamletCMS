<h3>{$heading}</h3>
<div id="js-postlist-data" class="ui bulleted list">
    {foreach $posts as $post}
        <div class="item">
            <a href="/blogs/{$post->blog_id}/posts/{$post->link}">{$post->title}</a>
        </div>
    {/foreach}
</div>