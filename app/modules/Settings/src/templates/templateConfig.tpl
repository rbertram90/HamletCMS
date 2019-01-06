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
            <form method="POST" class="ui form">
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
                    <p class="small">This is useful for importing fonts from <a href="https://fonts.google.com/">Google Fonts</a>. To remove an import, set the value to blank and save</p>
                    {foreach $config.Imports as $path}
                        <input type="text" name="imports[{$path@iteration}]" value="{$path}">
                    {/foreach}
                    <button class="ui labeled icon button" id="add_import"><i class="ui icon plus"></i> Add</button>
                </div>

                <button class="ui teal labeled icon button"><i class="ui icon save"></i> Save</button>
            </form>
        </div>
    </div>
</div>

<script>
    var nextImportIndex = {count($config.Imports)} + 1;

    $("#add_import").click(function() {
        $(this).before('<input type="text" name="imports[' + nextImportIndex + ']" value="">');
        return false;
    });
</script>