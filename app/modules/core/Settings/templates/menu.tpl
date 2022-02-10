<div class="ui grid">
    <div class="one column row">
        <div class="column">
            <div class="ui teal message">
                This section allows you to change the look and feel of the blog.
            </div>
        </div>
    </div>

    <div class="row">
        <div class="column">
            <h3 class="ui header">General</h3>
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

    <div class="row">
        <div class="column">
            <h3 class="ui header">Design</h3>
        </div>
    </div>
    
    <div class="two columns row">
        
        <div class="column">
        {*
            <div class="ui segment clearing">
                <h4 class="ui header">
                    <i class="paint brush icon"></i>
                    <div class="content">
                        <a href="/cms/settings/blogdesigner/{$blog->id}">Customise Design <em>(OUT OF ACTION!)</em></a>
                        <div class="sub header">Fine tune the look of your blog</div>
                    </div>
                </h4>
            </div>
        *}
            <div class="ui segment clearing">
                <h4 class="ui header">
                    <i class="columns icon"></i>
                    <div class="content">
                        <a href="/cms/settings/template/{$blog->id}">Change Template</a>
                        <div class="sub header">Choose from our pre-made designs</div>
                    </div>
                </h4>
            </div>
            <div class="ui segment clearing">
                <h4 class="ui header">
                    <i class="calculator icon"></i>
                    <div class="content">
                        <a href="/cms/settings/widgets/{$blog->id}">Configure Widgets</a>
                        <div class="sub header">Change what is shown on the sidebar of your blog</div>
                    </div>
                </h4>
            </div>
        </div>

        <div class="column">
            <div class="ui segment clearing">
                <h4 class="ui header">
                    <i class="calculator icon"></i>
                    <div class="content">
                        <a href="/cms/settings/templateConfig/{$blog->id}">Configure template</a>
                        <div class="sub header">Configure settings for the selected template</div>
                    </div>
                </h4>
            </div>
            <div class="ui segment clearing">
                <h4 class="ui header">
                    <i class="code icon"></i>
                    <div class="content">
                        <a href="/cms/settings/stylesheet/{$blog->id}">Edit Stylesheet</a>
                        <div class="sub header">Ideal for advanced users</div>
                    </div>
                </h4>
            </div>
            <div class="ui segment clearing">
                <h4 class="ui header">
                    <i class="trash alternate icon"></i>
                    <div class="content">
                        <a href="/cms/blog/delete/{$blog->id}">Delete Blog</a>
                        <div class="sub header">Completely erase this blog</div>
                    </div>
                </h4>
            </div>
        </div>
        
    </div>
    
    <style>.ui.segment img.floated { margin-bottom:0px; width:44px; }</style>
</div>