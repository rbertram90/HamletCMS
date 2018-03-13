<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/blog/overview/{$blog['id']}", $blog['name'], "/settings/menu/{$blog['id']}", 'Settings'), 'Edit Stylesheet')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('Edit Stylesheet', 'css.png', $blog['name'])}
            
            <form action="/settings/stylesheet/{$blog.id}" method="POST" class="ui form">

                <div class="field">
                    <label for="fld_css">CSS</label>

                    <div class="ui segment secondary"><i style="font-weight:normal;">Please beware this feature is targeted at
                        advanced users, if you don't want to completely ruin your blog I suggest using the
                        <a href="/config/{$blog.id}/blogdesigner">blog designer</a>!</i>
                    </div>
                    
                    <textarea name="fld_css" id="fld_css" style="height:600px; font-family:monospace;">{strip}
                        {file_get_contents("{$serverroot}/app/public/blogdata/{$blog['id']}/default.css")}
                    {/strip}</textarea>

                </div>

                <input type="button" class="ui button right floated" value="Cancel" name="goback" onclick="window.history.back()" />
                <input type="submit" class="ui button teal right floated" name="submit_update" value="Save" />

            </form>
        </div>
        
    </div>
</div>