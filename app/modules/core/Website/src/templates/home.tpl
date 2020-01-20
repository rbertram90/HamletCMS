<div class="ui segments">
    <h2 class="ui top attached header">What is this page?</h2>
    <div class="ui teal segment">
        <p>This is the default front-page for a website using <a href="http://github.com/rbertram90/HamletCMS" target="_blank">HamletCMS</a> as a backend content management system. These pages will likely include links to the blogs that have been created within the CMS. There will be a range of example views made available to place on these pages <em>(coming soon!)</em>.</p>
        <p>To change the contents of this file edit <strong>/app/modules/core/Website/src/templates/home.tpl</strong></p>
        <a href="/cms" class="ui teal button">Log into CMS</a>
    </div>
</div>

<div class="ui segments">
    <h2 class="ui top attached header">Adding further pages</h2>
    <div class="ui secondary teal segment">
        <ol class="ui list">
            <li>Add a public function to /app/controller/public_controller.inc.php
            <li>Name the function the same as the url path - so for an about page (example.com/about) the function should be called 'about'
            <li>Note: the terms 'blogs', 'cms' and 'api' are already used so don't use them!
            <li>Call $this->response->write('&lt;template-path&gt;'); to output a smarty file (<a href="https://github.com/rbertram90/core/wiki/response" target="_blank">see full response documentation</a>)
            <li>Create the smarty template file under <strong>/app/view/smarty/public</strong>
            <li>Note: by default <a href="https://semantic-ui.com/">semantic UI</a> is included
        </ol>
    </div>
</div>

<div class="ui segments">
    <h2 class="ui top attached header">
        Help and support
        <div class="sub header">Check out the <a href="https://github.com/rbertram90/HamletCMS/wiki">HamletCMS Wiki on Github</a> for documentation.</div>
    </h2>
    <div class="ui secondary teal segment">
        <div class="ui grid">
            <div class="two column row">
                <div class="column">
                    <div class="ui relaxed divided list">
                        <div class="item">
                            <a href="https://github.com/rbertram90/HamletCMS/wiki/Creating-modules" class="header" target="_blank">Creating modules</a>
                            <div class="description">Learn how to extend HamletCMS with your own code.</div>
                        </div>
                        <div class="item">
                            <a href="https://github.com/rbertram90/HamletCMS/wiki/Applying-custom-blog-domain-names" class="header" target="_blank">Applying custom blog domain names</a>
                            <div class="description">Need to connect a custom domain to one of your blogs? Find out how to in this wiki.</div>
                        </div>
                        <div class="item">
                            <a href="https://github.com/rbertram90/HamletCMS/wiki/Blog-templates" class="header" target="_blank">Blog templates</a>
                            <div class="description">More information on templates in HamletCMS.</div>
                        </div>
                    </div>
                </div>
                <div class="column">
                    <div class="ui relaxed divided list">
                        <div class="item">
                            <a href="https://github.com/rbertram90/HamletCMS/wiki/User-permissions" class="header" target="_blank">User permissions</a>
                            <div class="description">How to control the access contributors to your blog have.</div>
                        </div>
                        <div class="item">
                            <a href="https://github.com/rbertram90/HamletCMS/wiki/Injecting-custom-behavior-with-hooks" class="header" target="_blank">Injecting custom behaviour with hooks</a>
                            <div class="description">Hooks provide a method for custom modules to alter existing application logic.</div>
                        </div>
                        <div class="item">
                            <a href="https://github.com/rbertram90/HamletCMS/wiki/Modifying-the-post-teaser-template" class="header" target="_blank">Modifying the post teaser template</a>
                            <div class="description">How to make changes to how posts are displayed on your blog main page.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="ui segments">
    <h2 class="ui top attached header">
        Blogs on this website
        <div class="sub header">Example of how you could use the data from blogs to power content in your main site</div>
    </h2>
    <div class="ui tertiary teal segment">
        <div class="ui compact menu">
            <div class="ui simple dropdown item">
                Browse by title
                <i class="dropdown icon"></i>
                <div class="menu browse-by-letter">
                    {foreach from=$lettercounts key=letter item=count}
                        {if $count == 0}
                            <a class="item disabled">{$letter}</a>
                        {else}
                            <a class="item">{$letter}</a>
                        {/if}
                    {/foreach}
                </div>
            </div>
        </div>
        <div class="ui compact menu">
            <div class="ui simple dropdown item">
                Browse by category
                <i class="dropdown icon"></i>
                <div class="menu browse-by-category">
                    {foreach from=$categorycounts item=count}
                        <a class="item">{ucfirst($count.category)}</a>
                    {/foreach}
                </div>
            </div>
        </div>
    </div>
    <div class="ui secondary segment" id="browse-results">
        <!-- Browse results loaded here -->
        Select an option from the dropdowns to discover more blogs
    </div>
</div>

<style>.ui.segments { margin: 2.5rem 0; } </style>

<script>
    var generateBlogsList = function(blogs) {
        if (!blogs.length) return;
        var list = "";
        for (var i = 0; i < blogs.length; i++) {
            blog = blogs[i];
            list += '<div class="item"><div class="content">';
            list += '<div class="header"><a href="/blogs/' + blog.id + '" target="_blank">' + blog.name + '</a></div>';
            list += '<div class="description">' + blog.description + '</div></div></div>';
        }
        return '<div class="ui relaxed divided list">' + list + '</div>';
    };

    // API call to get blog data
    var loadBlogsByLetter = function (letter) {
        $.get('/api/blogs/byLetter', { 'letter': letter }, function(data) {
            $('#browse-results').html(generateBlogsList(data));
        });
    };

    var loadBlogsByCategory = function (category) {
        $.get('/api/blogs/byCategory', { 'category': category }, function(data) {
            $('#browse-results').html(generateBlogsList(data));
        });
    };

    var setResultsLoading = function() {
        $('#browse-results').html('<img src="/images/ajax-loader.gif" alt="Loading...">');
    };

    // Add event listener to dropdown
    $(".browse-by-letter > a").click(function() {
        setResultsLoading();
        loadBlogsByLetter($(this).html());
    });

    // Add event listener to dropdown
    $(".browse-by-category > a").click(function() {
        setResultsLoading();
        loadBlogsByCategory($(this).html());
    });

    // Init
    loadBlogsByCategory('General');
</script>

<a href="/cms" class="ui teal button">Login to CMS</a>