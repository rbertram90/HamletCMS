<div class="ui grid">
    <div class="row">
        <div class="column">
            {viewCrumbtrail(array("/blog/overview/{$blog['id']}", "{$blog['name']}"), 'Contributors')}
        </div>
    </div>
    <div class="row">
        <div class="column">
            {viewPageHeader('Contributors', 'friends.png', "{$blog['name']}")}
        </div>
    </div>
    <div class="row">
        <div class="column">
            <h2>Groups</h2>
            <p class="ui message">Groups allow you to define the set of actions that a contributor can perform on a granular level</p>
            <div class="ui segments">
                {foreach $groups as $group}
                    <div class="ui segment">
                        <div class="ui small header">
                            {if $group.locked == 0}
                                <a href="/contributors/editgroup/{$group['id']}" class="ui right floated button">Edit Permissions</a>
                            {/if}
                            <div class="content">{$group.name}</div>
                            <div class="sub header">{$group.description}</div>
                        </div>
                    </div>
                {/foreach}
            </div>

            <div class="ui hidden divider"></div>

            <a href="/contributors/creategroup/{$blog.id}" class="ui teal button">Add Group</a>
        </div>
    </div>
    <div class="row">
        <div class="column">
            <h2>Contributors</h2>
            <div class="ui five cards contributors-list">
            {foreach $contributors as $contributor}
                <div class="card">
                    <div class="image">
                        {if strlen({$contributor.profile_picture}) > 0 and trim({$contributor.profile_picture}) != "profile_default.jpg"}
                            <img src="/avatars/thumbs/{$contributor.profile_picture}">
                        {elseif $contributor.gender == 'Female'}
                            <img src="/avatars/default_woman.png">
                        {else}
                            <img src="/avatars/default_man.png">
                        {/if}
                    </div>

                    <div class="content">
                        <div class="header">
                            <a href="/account/user/{$contributor.id}">{$contributor.name} {$contributor.surname}</a>
                            {if $blog.user_id == $contributor.id}(owner){/if}
                        </div>
                        <div class="meta">
                            {$contributor.groupname}
                        </div>
                        <div class="description">
                            {$contributor.description}
                        </div>
                    </div>
                    <div class="extra content">
                        <span class="right floated">
                            {if $blog.user_id != $contributor.id}
                                <a href="/contributors/edit/{$blog.id}/{$contributor.id}"><i class="edit icon"></i></a>
                                <a href="/contributors/remove/{$blog.id}/{$contributor.id}" onclick="return confirm('Are you sure you want to remove this contributor from the blog?');"><i class="delete icon"></i></a>
                            {/if}
                        </span>
                        <span>
                            Joined in {$contributor.signup_date|date_format:'%b %Y'}
                        </span>
                    </div>
                </div>
            {/foreach}
            </div>

            <div class="ui hidden divider"></div>

            <a href="/contributors/create/{$blog.id}" class="ui teal button">Add Contributor</a>
        </div>
    </div>
</div>
