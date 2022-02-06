{if strpos($user->profile_picture, 'default') !== FALSE}

<img src="/hamlet/avatars/profile_default.jpg" alt="[Profile Photo]" class="user-card-icon">
{else}

<img src="/hamlet/avatars/thumbs/{$user->profile_picture}" alt="[Profile Photo]" class="user-card-icon">
{/if}

<h4>{$user->username}</h4>
<p>{$user->name} {$user->surname}</p>