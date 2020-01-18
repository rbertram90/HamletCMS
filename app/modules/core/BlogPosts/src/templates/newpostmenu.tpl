{* New Post Menu *}
    
<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/cms/blog/overview/{$blog->id}", "{$blog->name}"), 'Create New Post')}
        </div>
    </div>

    <div class="one column row">
        <div class="column">
            {viewPageHeader('New Post', 'doc_add.png', $blog->name)}
        </div>
    </div>

    {foreach name=menuitems from=$menu item=menuitem}
        <div class="eight wide column">
            <div class="ui segment clearing">
                <h4 class="ui header">
                    <i class="{$menuitem->icon} icon"></i>
                    <div class="content">
                        <a href="{$menuitem->url}">{$menuitem->text}</a>
                        <div class="sub header">{$menuitem->subtext}</div>
                    </div>
                </h4>
            </div>
        </div>
    {/foreach}
    
</div>