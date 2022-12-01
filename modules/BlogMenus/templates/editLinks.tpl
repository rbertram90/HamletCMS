<div class="ui grid">
    <div class="one column row">
        <div class="column">
            <h2>Menu options</h2>
            <form class="ui form" method="POST">
                <div class="field">
                    <label for="name">Menu name</label>
                    <input type="text" id="name" name="name" value="{$menu->name}">
                </div>
                <div class="field">
                    <label for="sort">Sort items</label>
                    <select name="sort" id="sort">
                        <option value="text">Text</option>
                        <option value="custom">Custom</option>
                    </select>
                </div>
                <button class="ui teal labeled icon button"><i class="save icon"></i>Save</button>
                <script>
                    $("#sort").val('{$menu->sort}');
                    $("#sort").dropdown();
                </script>
            </form>
        </div>
    </div>

    <div class="one column row">
        <div class="column">
            <h2>Items</h2>
            <div class="ui segments">
                {$items = $menu->items()}
                {foreach $items as $item name=links}
                    <div class="ui clearing segment">
                        <a href="/cms/menus/deletelink/{$blog->id}/{$item->id}" class="ui labeled icon right floated button" onclick="return confirm('Are you sure you want to delete this link?');"><i class="delete icon"></i>Delete</a>
                        <a href="/cms/menus/editlink/{$blog->id}/{$item->id}" class="ui labeled icon right floated button"><i class="edit icon"></i>Edit</a>
                        {$item->text}
                        {if !$smarty.foreach.links.last && $menu->sort == 'custom'}
                            <a href="/cms/menus/movelinkdown/{$blog->id}/{$item->id}" class="ui icon right floated button"><i class="arrow down icon"></i></a>
                        {/if}
                        {if !$smarty.foreach.links.first && $menu->sort == 'custom'}
                            <a href="/cms/menus/movelinkup/{$blog->id}/{$item->id}" class="ui icon right floated button"><i class="arrow up icon"></i></a>
                        {/if}
                    </div>
                {foreachelse}
                    <p class="ui message info">This menu doesn't contain any links</p>
                {/foreach}
            </div>

            <a href="/cms/menus/addlink/{$blog->id}/{$menu->id}" class="ui teal labeled icon button"><i class="plus icon"></i>Add link</a>
        </div>
    </div>
</div>