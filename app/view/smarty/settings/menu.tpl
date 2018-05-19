<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/cms/blog/overview/{$blog.id}", {$blog.name}), 'Settings')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('Settings', 'sliders horizontal', {$blog.name})}
            <div class="ui secondary segment">
                This section allows you to change the look and feel of the blog.</p>
            </div>
            <h3 class="ui header">General</h3>
        </div>
    </div>

    <div class="two columns row">
        
        <div class="column">
            <div class="ui segment clearing">
                <h4 class="ui header">
                    <i class="wrench icon"></i>
                    <div class="content">
                        <a href="/cms/settings/general/{$blog.id}">Name &amp; Description</a>
                        <div class="sub header">Update the identity of your blog</div>
                    </div>
                </h4>
            </div>
            <div class="ui segment clearing">
                <h4 class="ui header">
                    <i class="table icon"></i>
                    <div class="content">
                        <a href="/cms/settings/header/{$blog.id}">Header</a>
                        <div class="sub header">Settings for your blog header</div>
                    </div>
                </h4>
            </div>
            <div class="ui segment clearing">
                <h4 class="ui header">
                    <i class="sitemap icon"></i>
                    <div class="content">
                        <a href="/cms/settings/pages/{$blog.id}">Pages</a>
                        <div class="sub header">Add posts to the blog menu</div>
                    </div>
                </h4>
            </div>
        </div>
        
        <div class="column">
            <div class="ui segment clearing">
                <h4 class="ui header">
                    <i class="copy outline icon"></i>
                    <div class="content">
                        <a href="/cms/settings/posts/{$blog.id}">Posts</a>
                        <div class="sub header">Change how posts are displayed</div>
                    </div>
                </h4>
            </div>
            <div class="ui segment clearing">
                <h4 class="ui header">
                    <i class="table icon"></i>
                    <div class="content">
                        <a href="/cms/settings/footer/{$blog.id}">Footer</a>
                        <div class="sub header">Settings for your blog footer</div>
                    </div>
                </h4>
            </div>
        </div>
    </div>

    <h3 class="ui header">Design</h3>
    
    <div class="two columns row">
        
        <div class="column">
            <div class="ui segment clearing">
                <h4 class="ui header">
                    <i class="paint brush icon"></i>
                    <div class="content">
                        <a href="/cms/settings/blogdesigner/{$blog.id}">Customise Design <em>(OUT OF ACTION!)</em></a>
                        <div class="sub header">Fine tune the look of your blog</div>
                    </div>
                </h4>
            </div>
            <div class="ui segment clearing">
                <h4 class="ui header">
                    <i class="columns icon"></i>
                    <div class="content">
                        <a href="/cms/settings/template/{$blog.id}">Change Template</a>
                        <div class="sub header">Choose from our pre-made designs</div>
                    </div>
                </h4>
            </div>
        </div>
        
        <div class="column">
            <div class="ui segment clearing">
                <h4 class="ui header">
                    <i class="calculator icon"></i>
                    <div class="content">
                        <a href="/cms/settings/widgets/{$blog.id}">Configure Widgets</a>
                        <div class="sub header">Change what is shown on the sidebar of your blog</div>
                    </div>
                </h4>
            </div>
            <div class="ui segment clearing">
                <h4 class="ui header">
                    <i class="code icon"></i>
                    <div class="content">
                        <a href="/cms/settings/stylesheet/{$blog.id}">Edit Stylesheet</a>
                        <div class="sub header">Ideal for advanced users</div>
                    </div>
                </h4>
            </div>
            <div class="ui segment clearing">
                <h4 class="ui header">
                    <i class="trash alternate icon"></i>
                    <div class="content">
                        <a href="/cms/blog/delete/{$blog.id}">Delete Blog</a>
                        <div class="sub header">Completely erase this blog</div>
                    </div>
                </h4>
            </div>
        </div>
        
    </div>
    
    <style>.ui.segment img.floated { margin-bottom:0px; width:44px; }</style>
</div>