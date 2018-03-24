<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/blog/overview/{$blog['id']}", "{$blog['name']}"), 'Contributors')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('Contributors', 'friends.png', "{$blog['name']}")}
        </div>
    </div>
</div>

<style>
    form { display:inline; }
</style>

{foreach $contributors as $contributor}{strip}

    {$userPostCount = 0}
    {$lastPost = '-'}

    {foreach $postcounts as $postCount}

        {if $postCount['author_id'] == $contributor['id']}

            {$userPostCount = $postCount['post_count']}
            {$lastPost = rbwebdesigns\core\dateFormatter::formatFriendlyTime($postCount['last_post'])}

            {break}
        {/if}

    {/foreach}

    <div class="contributor-card">
        
        {if $blog['user_id'] != $contributor['id']}
        
            <form action="/contributors/{$blog.id}/delete" id="remove-contributor-{$contributor.id}" method="POST" class="delete-contributor-form">
                {* Delete Action *}
                <input type="hidden" value="{$contributor.id}" name="fld_UserID" />
                <img src="/resources/icons/64/cross.png" onclick="if(confirm('Are you sure you want to remove this contributor?')) {ldelim}document.getElementById('remove-contributor-{$contributor.id}').submit(); {rdelim}"  title="Remove Contributor" style="height:20px; cursor:pointer;" />
            </form>
        
        {/if}

        {if strlen({$contributor.profile_picture}) > 0 and trim({$contributor.profile_picture}) != "profile_default.jpg"}
            <img src="/avatars/thumbs/{$contributor.profile_picture}" class="profile-photo" />
        {else}
            <img src="/avatars/profile_default.jpg" class="profile-photo" />
        {/if}

        <h3><a href="/account/user/{$contributor.id}">{$contributor.name} {$contributor.surname}</a>
        {if $blog['user_id'] == $contributor['id']}&nbsp;(creator){/if}</h3>
   
        <p style="font-size:0.8em; margin-bottom:32px;">{$userPostCount} Posts
        {if $userPostCount > 0}, Last posted {$lastPost}{/if}</p>
        
        {if $blog['user_id'] != $contributor['id']}

            Permissions:<br>
            <form action="/contributors/{$blog.id}/update" method="POST">
                {* Change Permissions *}
                <select name="fld_Permission" id="fld_Permission_{$contributor.id}">
                    <option value="postonly">Add/Edit Posts Only</option>
                    <option value="all">Full Access</option>
                </select>
                <input type="hidden" value="{$contributor.id}" name="fld_UserID" />&nbsp;
                <button type="submit">Update</button>
            </form>
            <script>document.getElementById('fld_Permission_{$contributor.id}').value = "{$contributor.privileges}";</script>

        {/if}
    </div>

{/strip}{/foreach}


<div class="contributor-card" style="text-align:center;">
    
    <img src="/resources/icons/64/avatar-light.png" style="height:70px; margin:5px;" /><br>
    <a href="/contributors/{$blog.id}/add" class="action_button" style="zmargin-top:40px;">Add Contributor</a>
</div>