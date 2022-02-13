<div class="ui grid">
    <div class="row">
        <div class="column">
            <h2>Groups</h2>
            <p class="ui teal message">Groups allow you to define the set of actions that a contributor can perform on a granular level</p>
            <div class="ui segments">
                {foreach $groups as $group}
                    <div class="ui clearing segment">
                        {if $group->locked == 0}
                            <a data-groupid="{$group->id}" class="ui red right floated labeled icon button confirm-delete-group" data-no-spinner="true"><i class="trash icon"></i>Delete</a><a href="/cms/contributors/editgroup/{$group->id}" class="ui right floated labeled icon button"><i class="edit icon"></i>Permissions</a>
                        {/if}
                        <div class="ui left floated small header">
                            <div class="content">{$group->name}</div>
                            <div class="sub header">{$group->description}</div>
                        </div>
                    </div>
                {/foreach}
            </div>

            <div class="ui hidden divider"></div>

            <a href="/cms/contributors/creategroup/{$blog->id}" class="ui labeled icon teal button"><i class="plus icon"></i>Add Group</a>
        </div>
    </div>
    <div class="row">
        <div class="column">
            <h2>Contributors</h2>
            <div class="ui five cards contributors-list">
            {foreach $contributors as $contributor}
                <div class="card">
                    <div class="image">
                        <img src="{$contributor->avatar()}" alt="Avatar">
                    </div>

                    <div class="content">
                        <div class="header">
                            <a href="/cms/account/user/{$contributor->id}">{$contributor->name} {$contributor->surname}</a>
                            {if $blog->user_id == $contributor->id}(owner){/if}
                        </div>
                        <div class="meta">
                            {$contributor->groupname}
                        </div>
                        <div class="description">
                            {$contributor->description}
                        </div>
                    </div>
                    <div class="extra content">
                        <span class="right floated">
                            {if $blog->user_id != $contributor->id}
                                <a href="/cms/contributors/edit/{$blog->id}/{$contributor->id}"><i class="edit icon"></i></a>
                                <a href="/cms/contributors/remove/{$blog->id}/{$contributor->id}" onclick="return confirm('Are you sure you want to remove this contributor from the blog?');"><i class="delete icon"></i></a>
                            {/if}
                        </span>
                        <span>
                            Joined in {$contributor->signup_date|date_format:'%b %Y'}
                        </span>
                    </div>
                </div>
            {/foreach}
            </div>

            <div class="ui hidden divider"></div>

            <a href="/cms/contributors/create/{$blog->id}" class="ui labeled icon teal button"><i class="plus icon"></i>Add Contributor</a>
            <a href="/cms/contributors/invite/{$blog->id}" class="ui labeled icon teal button"><i class="plus icon"></i> Invite Contributor</a>
        </div>
    </div>
</div>

<div class="ui basic modal" id="delete_group_modal">
  <div class="ui icon huge header">
    <i class="trash alternate outline icon"></i>
    <span class="heading-text">Delete group</span>
  </div>
  <div class="content" style="text-align:center;">
    <p>Are you sure you want to delete this group?</p>
  </div>
  <div class="actions" style="text-align:center;">
    <a class="big ui green ok inverted button" id="delete_group_button" data-no-spinner="true">
      <i class="checkmark icon"></i>
      Delete
    </a>
    <div class="big ui red basic cancel inverted button" data-no-spinner="true">
      <i class="remove icon"></i>
      Nevermind
    </div>
  </div>
</div>

<script>
    $(".confirm-delete-group").click(function(event) {
        event.preventDefault();

        $("#delete_group_modal")
            .find("#delete_group_button")
            .data("groupid", $(this).data('groupid'));

        $("#delete_group_modal")
            .modal('setting', 'closable', false)
            .modal('show');
    });
    $("#delete_group_button").click(function(event) {
        event.preventDefault();
        var groupID = $(this).data("groupid");

        $.ajax({
            url: '/api/contributors/deletegroup',
            type: 'post',
            data: {
                groupID: groupID,
                blogID: {$blog->id}
            }
        }).done(function (data, textStatus, jqXHR) {
            data = JSON.parse(jqXHR.responseText);
            $("#delete_group_modal .header .icon").removeClass('trash alternate outline');
            if (data.success) {
                $("#delete_group_modal .header .icon").addClass('green check');
                $("#delete_group_modal .header .heading-text").html('Group deleted');
            }
            else {
                $("#delete_group_modal .header .icon").addClass('red x');
                $("#delete_group_modal .header .heading-text").html('Delete failed');
            }
            $("#delete_group_modal .content p")
                .html(data.message);
            $("#delete_group_modal .actions")
                .html('<div class="big ui basic inverted button" id="finish_delete">Finish</div>');

            $("#finish_delete").click(function() {
                window.location.reload();
            });

        }).fail(function (jqXHR, textStatus, errorThrown) {
            data = JSON.parse(jqXHR.responseText);
            $("#delete_group_modal .content p").html(data.message);
        });

        return false;
    });
</script>