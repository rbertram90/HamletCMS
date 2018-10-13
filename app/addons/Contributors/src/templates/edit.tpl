<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(["/cms/blog/overview/{$blog.id}", "{$blog.name}", "/cms/contributors/manage/{$blog.id}", "Contributors"], 'Edit Contributor')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('Edit Contributor', 'user', "{$blog.name}")}
        </div>
    </div>

    <div class="one column row">
        <div class="column">
            <h2>Change group</h2>

            <form class="ui form" method="POST">
                <select class="ui dropdown" name="fld_group">
                {foreach $groups as $group}
                    <option value="{$group.id}">{$group.name}</option>
                {/foreach}
                </select>

                <button class="ui teal button">Update</button>
                <button type="button" class="ui button" onclick="window.history.back();">Cancel</button>
            </form>
    
        </div>
    </div>
</div>

<script>
$(".ui.dropdown").dropdown();
</script>