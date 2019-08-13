<div class="ui grid">

{if isset($link)}
    {$action = 'Edit'}    
{else}
    {$action = 'Add'}
{/if}

    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/cms/blog/overview/{$blog->id}", "{$blog->name}", "/cms/settings/menu/{$blog->id}", "Settings", "/cms/menus/manage/{$blog->id}", "Menus", "/cms/menus/edit/{$blog->id}/{$menu->id}", "{$menu->name}"), "{$action} menu link")}
        </div>
    </div>
    <div class="two column row">
        <div class="column">
            {viewPageHeader("{$action} menu link", 'sitemap', "{$menu->name}")}
        </div>
    </div>

    <div class="one column row">
        <div class="column">
            <form class="ui form" method="POST">
                <div class="field">
                    <label for="text">Link text</label>
                    <input type="text" name="text" id="text" required>
                </div>

                <div class="field">
                    <label for="type">Type</label>
                    <select name="type" id="type" class="ui dropdown">
                        <option value=""></option>
                        <option value="post">Post</option>
                        <option value="tag">Tag</option>
                        <option value="blog">Blog</option>
                        <option value="external">Webpage (URL)</option>
                        <option value="mail">E-mail address</option>
                        <option value="tel">Phone number</option>
                    </select>
                </div>

                <div class="field" id="post_wrapper" style="display:none;">
                    <label for="type">Post</label>
                    <div class="ui search selection dropdown" id="post">
                        <input type="hidden" value="" name="post_id" id="post_id">
                        <i class="dropdown icon"></i>
                        <input class="search" type="text" id="post_search_text">
                        <div class="text"></div>
                    </div>
                </div>

                <div class="field" id="blog_wrapper" style="display:none;">
                    <label for="type">Blog</label>
                    <div class="ui search selection dropdown" id="blog">
                        <input type="hidden" value="" name="blog_id" id="blog_id">
                        <i class="dropdown icon"></i>
                        <input class="search" type="text" id="blog_search_text">
                        <div class="text"></div>
                    </div>
                </div>

                <div class="field" id="target_wrapper" style="display:none;">
                    <label for="target">Link target</label>
                    <input type="text" name="target" id="target">
                </div>

                <div class="field" id="tags_wrapper" style="display:none;">
                    <label for="tag">Tag</label>
                    <select name="tag" id="tag" class="ui dropdown">
                        <option value=""></option>
                        {foreach $tags as $tag}
                            <option value="{$tag}">{ucfirst($tag)}</option>
                        {/foreach}
                    </select>
                </div>

                <div class="field">
                    <div class="ui checkbox">
                        <input type="checkbox" name="new_window" id="new_window">
                        <label for="new_window">Open link in new window?</label>
                    </div>
                </div>

                <button class="ui teal labeled icon button" type="submit" id="create_button"><i class="plus icon"></i>Create</button>
            </form>
        </div>
    </div>
</div>

<script>
$("#blog").dropdown({
    placeholder: 'Search for blog',
    minCharacters: 2,
    apiSettings: {
        url: '/api/blog/search?q={ldelim}query{rdelim}'
    }
});
$("#post").dropdown({
    placeholder: 'Search for post',
    minCharacters: 2,
    apiSettings: {
        url: '/api/posts/search?blogID={$blog->id}&q={ldelim}query{rdelim}'
    }
});
$("#type").dropdown();
$("#tag").dropdown();

$("#type").on('change', function() {

    $("#target_wrapper").hide();
    $("#post_wrapper").hide();
    $("#tags_wrapper").hide();
    $("#blog_wrapper").hide();

    switch($(this).children("option:selected").val()) {
        case 'blog':
            $("#blog_wrapper").show();
            break;
        case 'post':
            $("#post_wrapper").show();
            break;
        case 'external':
            $("#target_wrapper").show().find("label")
                .html('Web address');
            $("#target_wrapper input").attr('placeholder', 'https://www.example.com').val('');
            break;
        case 'mail':
            $("#target_wrapper").show().find("label")
                .html('Email address');
            $("#target_wrapper input").attr('placeholder', 'someone@example.com').val('');
            break;
        case 'tel':
            $("#target_wrapper").show().find("label")
                .html('Phone number');
            $("#target_wrapper input").attr('placeholder', '01234 123123').val('');
            break;
        case 'tag':
            $("#tags_wrapper").show();
            break;
    }
});

{if isset($link)}
    // Editing a link!

    // Populate values

    $("#text").val("{$link->text}");

    var type = "{$link->type}";

    $("#type").val(type);
    $("#type").dropdown('set selected', type);
    $("#type").trigger('change');

    switch(type) {
        case 'blog':
            $("#blog_id").val("{$link->link_target}");
            $("#blog .text").html('Blog #{$link->link_target}'); // todo: get the actual blog name!
            break;
        case 'post':
            $("#post_id").val("{$link->link_target}");
            $("#post .text").html('Post #{$link->link_target}'); // todo: get the actual post title!
            break;
        case 'tag':
            $("#tag").val("{$link->link_target}");
            $("#tag").dropdown('set selected', "{$link->link_target}");
            break;
        case 'external':
        case 'tel':
        case 'mail':
            $("#target").val("{$link->link_target}");
            break;
    }

    if ({$link->new_window}) {
        $("#new_window").attr("checked", "checked");
    }

    // Update UI
    $("#create_button").html('<i class="save icon"></i>Save');

{/if}

</script>