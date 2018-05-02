<div class="ui grid">
    <div class="row">
        <div class="column">
            {viewCrumbtrail(["/cms/blog/overview/{$blog['id']}", "{$blog['name']}", "/cms/contributors/manage/{$blog['id']}", "Contributors"], 'Add Group')}
        </div>
    </div>
    <div class="row">
        <div class="column">
            {viewPageHeader("Add Group", 'friends.png', "{$blog['name']}")}
        </div>
    </div>
    <div class="row">
        <div class="column">
            <form class="ui form" method="POST">
                <h2>Basic Information</h2>
                <div class="field">
                    <label for="fld_name">Group Name</label>
                    <input type="text" value="" name="fld_name" id="fld_name">
                </div>

                <div class="field">
                    <label for="fld_description">Description</label>
                    <input type="text" value="" name="fld_description" id="fld_description">
                </div>

                <div class="ui divider hidden"></div>

                <h2>Permissions</h2>
                <p class="ui visible warning message">Use these with caution, only give permissions to people you trust!</p>

                <div class="inline field">
                    <div class="ui checkbox">
                        <input class="hidden" id="perm_create_posts" type="checkbox" name="fld_permission[create_posts]">
                        <label>Create posts</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui checkbox">
                        <input class="hidden" id="perm_publish_posts" type="checkbox" name="fld_permission[publish_posts]">
                        <label>Publish posts</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui checkbox">
                        <input class="hidden" id="perm_edit_all_posts" type="checkbox" name="fld_permission[edit_all_posts]">
                        <label>Edit any post</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui checkbox">
                        <input class="hidden" id="perm_delete_posts" type="checkbox" name="fld_permission[delete_posts]">
                        <label>Delete any posts</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui checkbox">
                        <input class="hidden" id="perm_manage_comments" type="checkbox" name="fld_permission[manage_comments]">
                        <label>Manage comments</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui checkbox">
                        <input class="hidden" id="perm_delete_files" type="checkbox" name="fld_permission[delete_files]">
                        <label>Delete files</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui checkbox">
                        <input class="hidden" id="perm_change_settings" type="checkbox" name="fld_permission[change_settings]">
                        <label>Change blog settings</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui checkbox">
                        <input class="hidden" id="perm_manage_contributors" type="checkbox" name="fld_permission[manage_contributors]">
                        <label>Manage blog contributors</label>
                    </div>
                </div>

                <div class="ui divider hidden"></div>

                <button class="ui button teal">Save</button>
                <button type="button" class="ui button" onclick="window.history.back();">Cancel</button>
            </form>
        </div>
    </div>
</div>

<script>
    $('.ui.checkbox').checkbox();
</script>