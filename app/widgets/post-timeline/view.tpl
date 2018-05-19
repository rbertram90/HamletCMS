<div class="ui segments widget">
    <div class="ui segment">
        <h3>{$heading}</h3>

        <div id="js-postlist-data" class="ui bulleted list"><img src="/images/ajax-loader.gif" alt="Loading..."></div>
    </div>
</div>

{* Important note - if we're using custom domains then will need to allow CORS headers to access this /api/posts route *}

<script>
$.get('{$cms_url}/api/posts', { blogID: {$blog.id}, limit: {$maxposts} }, function(data) {
    var list = "";

    for (var i = 0; i < data.posts.length; i++) {
        var post = data.posts[i];
        list += '<div class="item"><a href="/blogs/' + post.blog_id + '/posts/' + post.link + '">' + post.title  +'</a></div>';
    }

    $("#js-postlist-data").html(list);
});
</script>