<div class="ui grid">
    <div class="row">
        <div class="column">
            {viewCrumbtrail(["/cms/blog/overview/{$blog->id}", "{$blog->name}", "/cms/contributors/manage/{$blog->id}", "Contributors"], 'Add Group')}
        </div>
    </div>
    <div class="row">
        <div class="column">
            {viewPageHeader("Add Group", 'user plus', "{$blog->name}")}
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

                {foreach $permissions as $permission}
                    <div class="inline field">
                        <div class="ui checkbox">
                            <input class="hidden" id="perm_{$permission.key}" type="checkbox" name="fld_permission[{$permission.key}]">
                            <label>{$permission.label}</label>
                        </div>
                    </div>
                {/foreach}

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