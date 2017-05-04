<h1>{$user.name} {$user.surname} ({$user.username})</h1>
<img src="/avatars/thumbs/{$user.profile_picture}" alt="Profile picture" />
<ul>
    <li><strong>Birthday</strong>: {date('F jS', strtotime($user.dob))}</li>
    <li><strong>Location</strong>: {$user.location}</li>
</ul>