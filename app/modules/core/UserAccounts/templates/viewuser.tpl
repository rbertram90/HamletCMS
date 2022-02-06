<div class="ui segments">
    <div class="ui clearing segment">
        <img src="/hamlet/avatars/thumbs/{$user->profile_picture}" alt="Profile picture" class="ui small circular left floated image">
        <h1>{$user->name} {$user->surname} ({$user->username})</h1>
        <p>{$user->description}</p>
    </div>
    <div class="ui horizontal segments">
        <div class="ui segment">
            <strong>Member since</strong>: {$user->signup_date|date_format}
        </div>
        <div class="ui segment">
            <strong>Last login</strong>: {$user->last_login|date_format}
        </div>
        {if $user->dob}
            <div class="ui segment">
                <strong>Birthday</strong>: {date('F jS', strtotime($user->dob))}
            </div>
        {/if}
        {if $user->location}
            <div class="ui segment">
                <strong>Location</strong>: {$user->location}
            </div>
        {/if}
    </div>
    <div class="ui segment">
        <div class="ui statistics">
            <div class="teal statistic">
                <div class="value">{$blogCount}</div>
                <div class="label">Contrib. blogs</div>
            </div>
            <div class="teal statistic">
                <div class="value">{$postCount}</div>
                <div class="label">Blog posts</div>
            </div>
        </div>
    </div>
    <div class="ui segment">
        <h3>Blogs</h3>
        <ul>
        {foreach $blogs as $blog}
            <li><a href="{$blog->url()}">{$blog->name}</a></li>
        {/foreach}
        </ul>
    </div>
</div>



{* Content fetched from content hook! *}
{$dynamicContent}