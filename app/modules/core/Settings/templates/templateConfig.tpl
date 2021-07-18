<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/cms/blog/overview/{$blog->id}", $blog->name, "/cms/settings/menu/{$blog->id}", 'Settings'), 'Template settings')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('Template settings', 'sliders horizontal', $blog->name)}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            <form method="POST" class="ui form" id="template_settings_form">
                <div class="field">
                    <label for="column_count">Number of columns</label>
                    <input type="number" name="column_count" id="column_count" value="{$config.Layout.ColumnCount}" max="3" min="1" required>
                </div>

                <div class="field">
                    <label for="post_column">Posts column</label>
                    <input type="number" name="post_column" id="post_column" value="{$config.Layout.PostsColumn}" max="3" min="1" required>
                </div>

                <div class="field">
                    <label for="post_column">CSS Imports</label>
                    <p class="ui small message" style="color: #666;">Imports are useful for importing fonts from <a href="https://fonts.google.com/">Google Fonts</a>. To remove an import, set the value to blank and save</p>
                    {foreach $config.Imports as $path}
                        <div class="field">
                            <input type="text" name="imports[{$path@iteration}]" value="{$path}">
                        </div>
                    {/foreach}
                    <button class="ui labeled icon button" id="add_import"><i class="ui icon plus"></i> Add</button>
                </div>

                <div class="field">
                    <label for="post_column">Widget zones</label>
                    <p class="ui small message" style="color: #666;">Zones are places within the template which widgets can appear. Additional zones can be defined here and added to the template with <code>{ldelim}$widgets.ZONENAME{rdelim}</code>. Deleting a zone will remove widgets from the blog, so you may wish to move them to a different zone beforehand.</p>
                    {foreach $config.Zones as $zone}
                        <div class="field">
                            {if $zone == 'leftpanel' || $zone == 'rightpanel'}
                                <input type="text" name="zones[{$zone@iteration}]" value="{$zone}" disabled>
                            {else}
                                <input type="text" name="zones[{$zone@iteration}]" value="{$zone}">
                            {/if}
                        </div>
                    {/foreach}
                    <button class="ui labeled icon button" id="add_zone"><i class="ui icon plus"></i> Add</button>
                </div>

                <button class="ui teal labeled icon button"><i class="ui icon save"></i> Save</button>
            </form>
        </div>
    </div>
</div>

<script>
    var nextImportIndex = {count($config.Imports)} + 1;
    var nextZoneIndex = {count($config.Zones)} + 1;

    $("#add_import").click(function() {
        $(this).before('<div class="field"><input type="text" name="imports[' + nextImportIndex + ']" value=""></div>');
        return false;
    });
    $("#add_zone").click(function() {
        $(this).before('<div class="field"><input type="text" name="zones[' + nextZoneIndex + ']" value=""></div>');
        return false;
    });

    $("#template_settings_form").submit(function() {
        // Check each of the zones are unique
        var $zones = $(this).find(":input[name^=zones]");
        var values = [];
        var valid = true;

        $zones.each(function() {
            var zone = $(this).val();
            if (zone.length > 0) {
                if (values.indexOf(zone) >= 0) {
                    $(this).css("border-color", "red");
                    valid = false;
                    return false;
                }
                values.push(zone);
            }
        });

        return valid;
    });
</script>