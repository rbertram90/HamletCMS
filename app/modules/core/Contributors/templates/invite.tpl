<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(["/cms/blog/overview/{$blog->id}", "{$blog->name}", "/cms/contributors/manage/{$blog->id}", "Contributors"], 'Invite')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('Invite contributor', 'user plus', "{$blog->name}")}
        </div>
    </div>
</div>

<form method="POST" class="ui form">
    <div id="form_errors">
    </div>

    <div class="field">
        <label for="group">Group</label>
        <select required name="group" id="field_group">
            <option>- Select -</option>
            {foreach $groups as $group}
                <option value="{$group->id}">{$group->name}</option>
            {/foreach}
        </select>
    </div>

    <div class="field">
        <label for="username">Username</label>
        <input type="text" required name="username" id="field_username">
    </div>

    <button class="ui labeled icon button" id="find_user" type="button"><i class="icon search"></i>Find user</button>

    <input type="hidden" value="" name="selected_user" id="selected_user">
    <div id="user_preview"></div>

</form>

<script>
    $("#find_user").click(function(e) {
        var username = $("#field_username").val();
        if (username.length == 0) {
            $("#form_errors").html('<p class="error">Please enter a username</p>');
        }
        $.getJSON("/api/user/get", { username: username }, function (data) {
            if (data.length == 1) {
                var user = data[0];
                $("#selected_user").val(user.id);
                $("#user_preview").html("<div class='ui card'>" +
                    "<div class='image'><img src='/avatars/" + user.profile_picture + "' alt='" + user.username + "'></div>" +
                    "<div class='content'><a class='header'>" + user.name + " " + user.surname + "</a>" +
                    "<div class='description'>" + user.description + "</div></div>" + 
                    "<button class='ui bottom attached teal button'><i class='add icon'></i>Invite</button></div>");
            }
        });
    });
</script>