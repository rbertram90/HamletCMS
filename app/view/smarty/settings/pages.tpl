<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/cms/blog/overview/{$blog.id}", {$blog.name}, "/settings/menu/{$blog.id}", 'Settings'), 'Configure Pages')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('Configure Pages', 'pages_gear.png', {$blog.name})}


            <h3 class="ui header">Current Pages</h3>
            <div class="ui secondary segment">
                Each post selected as a page will appear (in the order shown) as a link in your blog navigation menu.
            </div>

            {if count($pages) == 0}
                <p class='info'>No Pages Found</p>
            {else}

                <div class="ui segments">
                    {foreach from=$pages item=page}
                        <div class="ui clearing segment">
                        {if getType($page) == 'string'}
                            {substr($page,2)}
                
                            <form action="/cms/settings/pages/{$blog.id}/remove" method="POST" style="display:inline">
                                <input type="hidden" name="fld_postid" value="{$page}" />
                                <button class="ui button right floated" type="submit">Remove</button>
                            </form>
                            
                            <form action="/cms/settings/pages/{$blog.id}/down" method="POST" style="display:inline">
                                <input type="hidden" name="fld_postid" value="{$page}" />
                                <button class="ui button right floated" type="submit">&#x25BC;</button>
                            </form>
                            
                            <form action="/cms/settings/pages/{$blog.id}/up" method="POST" style="display:inline">
                                <input type="hidden" name="fld_postid" value="{$page}" />
                                <button class="ui button right floated" type="submit">&#x25B2;</button>
                            </form>

                        {else}
                            <a href="/blogs/{$blog.id}/posts/{$page.link}" target="_blank">{$page.title}</a>

                            <form action="/cms/settings/pages/{$blog.id}/remove" method="POST" style="display:inline">
                                <input type="hidden" name="fld_postid" value="{$page.id}" />
                                <button class="ui button right floated" type="submit">Remove</button>
                            </form>
                            
                            <form action="/cms/settings/pages/{$blog.id}/down" method="POST" style="display:inline">
                                <input type="hidden" name="fld_postid" value="{$page.id}" />
                                <button class="ui button right floated" type="submit">&#x25BC;</button>
                            </form>

                            <form action="/cms/settings/pages/{$blog.id}/up" method="POST" style="display:inline">
                                <input type="hidden" name="fld_postid" value="{$page.id}" />
                                <button class="ui button right floated" type="submit">&#x25B2;</button>
                            </form>
                        {/if}
                        </div>
                    {/foreach}
                </div>

            {/if}


            <h3 class="ui header">Add Page</h3>
            <div class="ui secondary segment">
                You can set blog posts or a tag as 'pages' which appear on the menu of your blog
            </div>


            <form action="/cms/settings/pages/{$blog.id}/add" method="POST" class="ui form">
                <div id="pagetype" class="field">
                    <label for="fld_pagetype">Page Type</label>
                    <select name="fld_pagetype" id="fld_pagetype" class="semantic-dropdown">
                        <option value="p" selected>Post</option>
                        <option value="t">Tag</option>
                    </select>
                </div>

                <div id="selectpost" class="field">
                    <label for="fld_postid">Post</label>
                    <select name="fld_postid" id="fld_postid" class="semantic-dropdown">
                        {foreach from=$posts item=post}
                            {if in_array({$post.id}, $pagelist) == false}
                                <option value="{$post.id}">{$post.title}</option>
                            {/if}
                        {/foreach}
                    </select>
                </div>
                <div id="selecttag" class="field" style="display:none;">
                    <label for="fld_tag">Tag</label>
                    <select name="fld_tag" id="fld_tag" class="semantic-dropdown">
                        {foreach from=$tags item=tag}
                            {if in_array($tag, $taglist) == false}
                                <option value="{$tag}">{$tag}</option>
                            {/if}
                        {/foreach}
                    </select>
                </div>
                <input type="submit" name="fld_submit" class="ui button teal" value="Add">
            </form>

            <script>
                $("#fld_pagetype").change(function() {
                    switch($(this).val()) {
                        case "t":
                            $("#selectpost").hide();
                            $("#selecttag").show();
                            break;

                        case "p":
                            $("#selecttag").hide();
                            $("#selectpost").show();
                            break;
                    }
                });
            </script>

            <script>
                // Apply semantic UI dropdown
                $(".semantic-dropdown").dropdown();
            </script>

            <input type="button" class="ui right floated button" value="Go Back" name="goback" onclick="window.history.back()" />
            
        </div>
    </div>
    
</div>