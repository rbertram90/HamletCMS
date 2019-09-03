<div class="ui grid">
    
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/cms/blog/overview/{$blog->id}", "{$blog->name}", "/cms/settings/menu/{$blog->id}", "Settings", "/cms/menus/manage/{$blog->id}", "Menus"), "{$menu->name}")}
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
                {$items = $menu->items()}
                {foreach $items as $item}
                    <div class="ui clearing segment">
                        <a href="/cms/menus/deletelink/{$blog->id}/{$item->id}" class="ui labeled icon right floated button" onclick="return confirm('Are you sure you want to delete this link?');"><i class="delete icon"></i>Delete</a>
                        <a href="/cms/menus/editlink/{$blog->id}/{$item->id}" class="ui labeled icon right floated button"><i class="edit icon"></i>Edit</a>
                        {$item->text}
                    </div>
                {foreachelse}
                    <p class="ui message info">This menu doesn't contain any links</p>
                {/foreach}
            </div>

            <a href="/cms/menus/addlink/{$blog->id}/{$menu->id}" class="ui teal labeled icon button"><i class="plus icon"></i>Add link</a>
        </div>
    </div>
</div>