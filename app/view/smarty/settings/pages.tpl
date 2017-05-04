{viewCrumbtrail(array("/overview/{$blog.id}", {$blog.name}, "/config/{$blog.id}", 'Settings'), 'Configure Pages')}
{viewPageHeader('Configure Pages', 'pages_gear.png', {$blog.name})}

<h3 style="margin-bottom:0;">Current Pages</h3>
<p style="margin-bottom:10px;">Each post selected as a page will appear (in the order shown) as a link in your blog navigation menu.</p>

{if count($pages) == 0}
    
    <p class='info'>No Pages Found</p>
    
{else}
    
    <table cellpadding="10" width="100%" style="margin-bottom:20px;">
        {foreach from=$pages item=page}
            <tr>
            {if getType($page) == 'string'}
                <td>{substr($page,2)}</td>
                <td style="text-align:right;">
                    <form action="/config/{$blog.id}/pages/up" method="POST" style="display:inline">
                        <input type="hidden" name="fld_postid" value="{$page}" />
                        <button type="submit">&#x25B2;</button>
                    </form>

                    <form action="/config/{$blog.id}/pages/down" method="POST" style="display:inline">
                        <input type="hidden" name="fld_postid" value="{$page}" />
                        <button type="submit">&#x25BC;</button>
                    </form>

                    <form action="/config/{$blog.id}/pages/remove" method="POST" style="display:inline">
                        <input type="hidden" name="fld_postid" value="{$page}" />
                        <button type="submit">Remove</button>
                    </form>
                </td>
            {else}
                <td>
                    <a href="/blogs/{$blog.id}/posts/{$page.link}" target="_blank">{$page.title}</a>
                </td>
                <td style="text-align:right;">

                    <form action="/config/{$blog.id}/pages/up" method="POST" style="display:inline">
                        <input type="hidden" name="fld_postid" value="{$page.id}" />
                        <button type="submit">&#x25B2;</button>
                    </form>

                    <form action="/config/{$blog.id}/pages/down" method="POST" style="display:inline">
                        <input type="hidden" name="fld_postid" value="{$page.id}" />
                        <button type="submit">&#x25BC;</button>
                    </form>

                    <form action="/config/{$blog.id}/pages/remove" method="POST" style="display:inline">
                        <input type="hidden" name="fld_postid" value="{$page.id}" />
                        <button type="submit">Remove</button>
                    </form>

                </td>
            {/if}
            </tr>
        {/foreach}
    </table>

{/if}


<h3 style="margin-bottom:0;">Add Page</h3>
<p style="margin-bottom:20px;">You can set blog posts or a tag as 'pages' which appear on the menu of your blog</p>


<form action="/config/{$blog.id}/pages/add" method="POST">
    
    <label for="fld_pagetype">Page Type</label>
    <select name="fld_pagetype" id="fld_pagetype">
        <option value="p" selected>Post</option>
        <option value="t">Tag</option>
    </select>
    
    <div id="selectpost">
        <label for="fld_postid">Post</label>
        <select name="fld_postid" id="fld_postid">

            {foreach from=$posts item=post}

                {if in_array({$post.id}, $pagelist) == false}
                    <option value="{$post.id}">{$post.title}</option>
                {/if}

            {/foreach}

        </select>
    </div>
    <div id="selecttag" style="display:none;">
        <label for="fld_tag">Tag</label>
        <select name="fld_tag" id="fld_tag">

            {foreach from=$tags item=tag}
                {if in_array($tag, $taglist) == false}
                    <option value="{$tag}">{$tag}</option>
                {/if}
            {/foreach}

        </select>
    </div>
    <input type="submit" name="fld_submit" value="Add" />
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

<div class="push-right">
    <input type="button" value="Go Back" name="goback" onclick="window.history.back()" />
</div>