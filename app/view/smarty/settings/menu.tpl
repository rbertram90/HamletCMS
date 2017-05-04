<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/overview/{$blog.id}", {$blog.name}), 'Settings')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('Settings', 'gear.png', {$blog.name})}
            <div class="ui secondary segment">
                This section allows you to change the look and feel of the blog. These can only be changed by blog administrators.</p>
            </div>
            <h3 class="ui header">General</h3>
        </div>
    </div>

    <div class="two columns row">
        
        <div class="column">
            <div class="ui segment clearing">
                <img src="/resources/icons/64/id.png" class="ui left floated image">
                <p><a href="/config/{$blog.id}/general">Name &amp; Description</a></p>
                <p>Update the identity of your blog</p>
            </div>
            <div class="ui segment clearing">
                <img src="/resources/icons/64/header.png" class="ui left floated image">
                <p><a href="/config/{$blog.id}/header">Header</a></p>
                <p>Settings for your blog header</p>
            </div>
            <div class="ui segment clearing">
                <img src="/resources/icons/64/pages_gear.png" class="ui left floated image">
                <p><a href="/config/{$blog.id}/pages">Pages</a></p>
                <p>Add posts to the blog menu</p>
            </div>
        </div>
        
        <div class="column">
            <div class="ui segment clearing">
                <img src="/resources/icons/64/pages_gear.png" class="ui left floated image">
                <p><a href="/config/{$blog.id}/posts">Posts</a></p>
                <p>Change how posts are displayed</p>
            </div>
            <div class="ui segment clearing">
                <img src="/resources/icons/64/footer.png" class="ui left floated image">
                <p><a href="/config/{$blog.id}/footer">Footer</a></p>
                <p>Settings for your blog footer</p>
            </div>
        </div>
    </div>

    <h3 class="ui header">Design</h3>
    
    <div class="two columns row">
        
        <div class="column">
            <div class="ui segment clearing">
                <img src="/resources/icons/64/paintbrush.png" class="ui left floated image">
                <p><a href="/config/{$blog.id}/blogdesigner">Customise Design</a></p>
                <p>Fine tune the look of your blog</p>
            </div>
            <div class="ui segment clearing">
                <img src="/resources/icons/64/star_doc.png" class="ui left floated image">
                <p><a href="/config/{$blog.id}/template">Change Template</a></p>
                <p>Choose from our pre-made designs</p>
            </div>
        </div>
        
        <div class="column">
            <div class="ui segment clearing">
                <img src="/resources/icons/64/oven_gear.png" class="ui left floated image">
                <p><a href="/config/{$blog.id}/widgets">Configure Widgets</a></p>
                <p>Change what is shown on the sidebar of your blog</p>
            </div>
            <div class="ui segment clearing">
                <img src="/resources/icons/64/css.png" class="ui left floated image">
                <p><a href="/config/{$blog.id}/stylesheet">Edit Stylesheet</a></p>
                <p>Ideal for Advanced Users</p>
            </div>
        </div>
        
    </div>
    
    <style>.ui.segment img.floated { margin-bottom:0px; width:44px; }</style>
</div>