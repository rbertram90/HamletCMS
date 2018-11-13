<div class="ui segment">
    <h2>What is this page?</h2>
    <p>This is the default front-page for a website using <a href="http://github.com/rbertram90/blog_cms" target="_blank">Blog CMS</a> as a backend content management system. These pages will likely include links to the blogs that have been created within the CMS. There will be a range of example views made available to place on these pages <em>(coming soon!)</em>.</p>

    <p>To change the contents of this file edit <strong>/app/view/smarty/public/home.tpl</strong></p>
</div>

<div class="ui segment">
    <h2>Adding further pages</h2>
    <ol>
        <li>Add a public function to /app/controller/public_controller.inc.php
        <li>Name the function the same as the url path - so for an about page (example.com/about) the function should be called 'about'
        <li>Note: the terms 'blogs', 'cms' and 'api' are already used so don't use them!
        <li>Call $this->response->write('&lt;template-path&gt;'); to output a smarty file (<a href="https://github.com/rbertram90/core/wiki/response" target="_blank">see full response documentation</a>)
        <li>Create the smarty template file under <strong>/app/view/smarty/public</strong>
        <li>Note: by default <a href="https://semantic-ui.com/">semantic UI</a> is included
    </ol>
</div>


<div class="ui segment">
    <h2>Blogs on this website</h2>
    <div class="ui segments">
        <div class="ui segment">
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
        <div class="ui segment" id="browse-results">
            <!-- Browse results loaded here -->
            Select an option from the dropdowns to discover more blogs
        </div>
    </div>
</div>

<script>
    var generateBlogsList = function(blogs) {
        if (!blogs.length) return;
        var list = "";
        for (var i = 0; i < blogs.length; i++) {
            blog = blogs[i];
            list += '<div class="item"><div class="content">';
            list += '<div class="header"><a href="/blogs/' + blog.id + '">' + blog.name + '</a></div>';
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
    // loadBlogsByLetter('0');
</script>

<a href="/cms" class="ui teal button">Login to CMS</a>