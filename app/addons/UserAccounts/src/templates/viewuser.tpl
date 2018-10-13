<div class="ui segments">
    <div class="ui clearing segment">
        <img src="/avatars/thumbs/{$user.profile_picture}" alt="Profile picture" class="ui small circular left floated image">
        <h1>{$user.name} {$user.surname} ({$user.username})</h1>
        <p>{$user.description}</p>
    </div>
    <div class="ui horizontal segments">
        <div class="ui segment">
            <strong>Birthday</strong>: {date('F jS', strtotime($user.dob))}
        </div>
        <div class="ui segment">
            <strong>Location</strong>: {$user.location}
        </div>
    </div>
</div>

{* Content fetched from content hook! *}
{$dynamicContent}