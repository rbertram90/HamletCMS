<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewPageHeader('All users', 'users')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            <table class="ui celled table">
                <thead>
                    <tr>
                        <th><a href="?sort=username">Username</a></th>
                        <th><a href="?sort=name">Name</a></th>
                        <th><a href="?sort=surname">Surname</a></th>
                        <th><a href="?sort=gender">Gender</a></th>
                        <th><a href="?sort=signup_date">Signup date</a></th>
                        <th><a href="?sort=last_login">Last login</a></th>
                    </tr>
                </thead>
                <tbody>
                {foreach $users as $user}
                    <tr>
                        <td><a href="/cms/account/user/{$user->id}">{$user->username}</a></td>
                        <td>{$user->name}</td>
                        <td>{$user->surname}</td>
                        <td>{$user->gender}</td>
                        <td>{$user->signup_date|date_format:"%H:%M %e %b, %Y"}</td>
                        <td>{$user->last_login|date_format:"%H:%M %e %b, %Y"}</td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
        </div>
    </div>
</div>