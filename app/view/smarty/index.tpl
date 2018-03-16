{viewPageHeader('Your Blogs', 'book.png')}

{* Check if this user contributes/ owns to at least 1 blog *}
{if count($blogs) > 0}

<table class="ui padded table">
    <thead>
        <tr>
        <th>Blog Name</th>
        <th>Contributors</th>
        <th class="collapsing"></th>
        <th class="collapsing"></th>
        </tr>
    </thead>
    <tbody>
        {* Loop through all the blogs this user can contribute to *}
        {foreach from=$blogs item=blog}
            <tr>
                <td>
                    <a href="/blog/overview/{$blog.id}" title="{$blog.name}" style="font-size:120%;">{$blog.name}</a>
                    <br><span class="date">{$blog.latestpost.timestamp}</span>
                </td>
                <td>
                    {foreach from=$blog.contributors item=contributor name=contributors}
                        
                        <a href="/account/user/{$contributor.id}" class="user-link">
                        {* Remove whitespace after name *}
                        {strip}
                            {if $contributor.id == $smarty.session.user}
                                <span data-userid="{$contributor.id}">You</span>
                            {else}
                                <span data-userid="{$contributor.id}">{$contributor.username}</span>
                            {/if}

                            {* Output a comma if this isn't the last item *}
                            {if !$smarty.foreach.contributors.last},{/if}
                        {/strip}
                        </a>
                        
                    {/foreach}

                    <script>
                      $(".user-link").mouseenter(function() {ldelim}showUserProfile($(this), "{$smarty.const.CLIENT_ROOT_ABS}", "{$clientroot_blogcms}"){rdelim});
                      $(".user-link").mouseleave(function() {ldelim}hideUserProfile($(this)){rdelim});
                    </script>
                </td>
                <td>
                    <div class="ui compact menu">
                        <div class="ui simple dropdown item single line blue">
                            - Actions -
                            <i class="dropdown icon"></i>
                            <div class="menu">
                                <a href="/posts/manage/{$blog.id}" class="item">Manage Current Posts</a>
                                <a href="/posts/create/{$blog.id}" class="item">Create New Post</a>
                                <a href="/contributors/{$blog.id}" class="item">Contributors</a>
                                <a href="/settings/menu/{$blog.id}" class="item">Blog Settings</a>
                                <a href="/files/{$blog.id}" class="item">Files</a>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <a href="/blogs/{$blog.id}" class="ui button teal single line" target="_blank">View Blog</a>
                </td>
            </div>

        {/foreach}
    </tbody>
</table>

{* This user doesn't have any blogs *}
{else}

    <p class="info">You aren't contributing to any blogs, why not <a href="/newblog">create a blog</a>?</p>

{/if}


{* todo: recent updates from blogs the user has subscribed to - see: /app/views/favorite_blogs_summary.php *}