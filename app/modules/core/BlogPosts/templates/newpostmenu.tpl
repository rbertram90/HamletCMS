{* New Post Menu *}
    
<div class="ui grid">
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