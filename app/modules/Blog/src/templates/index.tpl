<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewPageHeader('Your Blogs', 'book')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">

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
                    <a href="/cms/blog/overview/{$blog.id}" title="{$blog.name}" style="font-size:120%;">{$blog.name}</a>
                    <br><span class="date">{$blog.latestpost.timestamp}</span>
                </td>
                <td>
                    {foreach from=$blog.contributors item=contributor name=contributors}
                        
                        <a href="/cms/account/user/{$contributor.id}" class="user-link">
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
                        $(".user-link").mouseenter(function() {
                            showUserProfile($(this));
                        });

                        $(".user-link").mouseleave(function() {
                            hideUserProfile($(this));
                        });
                    </script>
                </td>
                <td>
                    <div class="ui compact menu">
                        <div class="ui simple dropdown item single line blue">
                            - Actions -
                            <i class="dropdown icon"></i>
                            <div class="menu">
                                {foreach $blog.actions as $action}
                                    <a href="{$action->url}" class="item">{$action->text}</a>
                                {/foreach}
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

<a href="/cms/blog/create" class="ui right floated teal button">Create Blog</a>

{* This user doesn't have any blogs *}
{else}

    <p class="ui message info">You're not contributing to any blogs, why not <a href="/cms/blog/create">create your first blog</a>?</p>

{/if}
        </div>
    </div>
</div>

{* todo: recent updates from blogs the user has subscribed to - see: /app/views/favorite_blogs_summary.php *}