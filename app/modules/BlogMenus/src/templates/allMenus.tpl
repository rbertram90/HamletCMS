<div class="ui grid">
    
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/cms/blog/overview/{$blog->id}", "{$blog->name}", "/cms/settings/menu/{$blog->id}", "Settings"), 'Menus')}
        </div>
    </div>
    
    <div class="two column row">
        <div class="column">
            {viewPageHeader('Menus', 'sitemap', "{$blog->name}")}
        </div>
    </div>

    <div class="one column row">
        <div class="column">
            <div class="ui segments">
                {foreach $menus as $menu}
                    <div class="ui clearing segment">
                        <a href="/cms/menus/delete/{$blog->id}/{$menu->id}" class="ui labeled icon right floated button" onclick="return confirm('Are you sure you want to delete this menu?');"><i class="delete icon"></i>Delete</a>
                        <a href="/cms/menus/edit/{$blog->id}/{$menu->id}" class="ui labeled icon right floated button"><i class="edit icon"></i>Edit</a>
                        {$menu->name}
                    </div>
                {foreachelse}
                    <p class="ui message info">No menus have been created yet</p>
                {/foreach}
            </div>

            <h2>Create menu</h2>
            <form class="ui form" method="POST" action="/cms/menus/create/{$blog->id}">
                <div class="field">
                    <label for="menu_name">Menu name</label>
                    <input type="text" name="menu_name" id="menu_name" required>
                </div>
                <button class="ui teal labeled icon button"><i class="plus icon"></i>Add menu</button>
            </form>
        </div>
    </div>
</div>