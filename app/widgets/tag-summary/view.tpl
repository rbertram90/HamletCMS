<div class="ui segments widget">
    <div class="ui segment">
        <h3>{$heading}</h3>

        {if $display == 'list'}
            <div id="js-taglist-data" class="ui bulleted list"><img src="/images/ajax-loader.gif" alt="Loading..."></div>
        {else}
            <div id="js-taglist-data" class="ui labels"><img src="/images/ajax-loader.gif" alt="Loading..."></div>
        {/if}
    </div>
</div>

<script>
$.get('{$cms_url}/api/tags', { blogID: {$blog->id}, sort: '{$sort}' }, function(data) {
    var list = "";

    for (var i = 0; i < data.length; i++) {
        var tag = data[i];

        if (i >= {$numtoshow}) break;
        if (tag.count < {$lowerlimit}) continue;

        if ('{$display}' == 'list') {
            list += '<div class="item"><a href="/blogs/{$blog->id}/tags/' + tag.text + '">' + tag.text  +'</a></div>';
        }
        else {
            list += '<a href="/blogs/{$blog->id}/tags/' + tag.text + '" class="ui label">' + tag.text + '<div class="detail">' + tag.count +'</div></a>';
        }
    }

    $("#js-taglist-data").html(list);
});
</script>