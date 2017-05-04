<script>
    function toggleElement(elemname)
    {
        var elem = document.getElementById(elemname);
        if(elem.style.display == 'none')
        {
            elem.style.display = 'block';
        }
        else
        {
            elem.style.display = 'none';
        }
    }
</script>
<style>
    .docs h3 {
        cursor: pointer
    }
    .docs h3:hover {
        text-decoration: underline;
    }
</style>

<div class="docs">
    <h1>Documentation</h1>

    <h2>General</h2>

    <h3 onclick="toggleElement('corefunctions');">Core Functions</h3>
        
    <div id="corefunctions" style="display:none;">
        <h4>redirect(<span class="type">string</span> url)</h4>
        <p>Short version of using PHP header function</p>
        
        <h4>setSystemMessage(<span class="type">string</span> message, <span class="type">string</span> type)</h4>
        <p>Show a flash message at top of page, pass in 'success' for a green one and 'error' for red</p>
        
        <h4>printArray(array)</h4>
        
        <h4>sanitize_string(mixed)</h4>
        <h4>sanitize_number(mixed)</h4>
        <h4>sanitize_float(mixed)</h4>
        <h4>sanitize_email(string)</h4>
        <h4>sanitize_timestamp(string)</h4>
    </div>
        
        
    
    <h2>Views</h2>

    <h3 onclick="toggleElement('views');">Function List</h3>
        
    <div id="views" style="display:none;">
        <h4>setVar(<span class="type">string</span> name, <span class="type">mixed</span> value)</h4>
        <p>Pass a variable through the view</p>
        
        <h4>setPageTitle(<span class="type">string</span> title)</h4>
        <p>Set the page title</p>
        
        <h4>setPageDescription(<span class="type">string</span> description)</h4>
        <p>Set the page meta description</p>
        
        <h4>clearVars()</h4>
        <p>Remove all variables passed into view</p>
        
        <h4>render(<span class="type">string</span> templatePath)</h4>
        <p>Run a view</p>
        
        <h4>addStylesheet(<span class="type">string</span> path)</h4>
        <p>Include a stylesheet in the view - provide path reletive to /</p>
        
        <h4>addScript(<span class="type">string</span> path)</h4>
        <p>Include a script file in the view - provide path reletive to /</p>
    </div>
    
    
    
    <h2>Constants</h2>

    <h3 onclick="toggleElement('user');">User</h3>
        
    <div id="user" style="display:none;">
        <h4>USER_ID</h4>
        <p>(int) ID from users table of current logged in user</p>
        
        <h4>USER_AUTHENTICATED</h4>
        <p>(boolean) Is the user logged in?</p>
    </div>
    
    <h3 onclick="toggleElement('routes');">Routes</h3>
    
    <div id="routes" style="display:none;">
        <p>Note: all paths should not include a trailing slash</p>
        
        <h4>SERVER_ROOT</h4>
        <p>Path to the main app folder</p>
        <h4>SERVER_PATH_WWW_ROOT</h4>
        <h4>SERVER_PATH_TEMPLATES</h4>
        <h4>SERVER_PATH_BLOGS</h4>
        <h4>SERVER_AVATAR_FOLDER</h4>
    </div>
    
    <h3 onclick="toggleElement('tables');">Tables</h3>
        
    <div id="tables" style="display:none;">
        <h4>TBL_BLOGS</h4>
        <h4>TBL_POSTS</h4>
        <h4>TBL_POST_VIEWS</h4>
        <h4>TBL_AUTOSAVES</h4>
        <h4>TBL_COMMENTS</h4>
        <h4>TBL_CONTRIBUTORS</h4>
        <h4>TBL_FAVOURITES</h4>
        <h4>TBL_USERS</h4>
    </div>
    
    
    
    
    <h2>Models</h2>

    <h3 onclick="toggleElement('modelblog');">Blogs</h3>
        
    <div id="modelblog" style="display:none;">
        <h3>Constructor</h3>
        <h4>$modelBlogs = new ClsBlog($dbconn);</h4>
        <p>Construct a new instance of ClsBlogs passing in the DB connection to the blogs database.</p>

        <h3>Functions</h3>
        <h4><span class="type">array</span> getBlogsByLetter(<span class="type">char</span> $letter)</h4>
        <p>Fetch all blogs where $letter matches the first character of the blog name.</p>

        <h4><span class="type">array</span> countBlogsByLetter()</h4>
        <p>Fetch a count of all blogs grouped by the first character.</p>

        <h4><span class="type">array</span> getBlogById(<span class="type">int</span> $id)</h4>
        <p>Fetch all information held in the blogs table for a blog with id = $id.</p>

        <h4><span class="type">array</span> getBlogsByUser(<span class="type">int</span> $userid)</h4>
        <p>Fetch all information held in the blogs table for blogs with user-id = $id.</p>

        <h4>createBlog(<span class="type">string</span> $pname, <span class="type">string</span> $pdesc, <span class="type">int</span> $pkey)</h4>
        <p>Create a new blog with given name, description and id which has already been generated.</p>

        <h4>deleteBlog($blogid) -- not implemented yet!</h4>
        <p>I will (eventually) have the option to completely delete a blog, however this is more complex than it first seems as not only does the blog need removing from database and physical files but so to do the posts, comments, contributors associated with the blog.</p>

        <h4>updateBlog($blogid, $paramNewValues)</h4>
        <p>This is the second version of this function which is more generic and allows any of the matching DB fields to be passed in the array paramater $paramNewValues. This means I can update the database without having to change this part of the code.</p>

        <h4>updateWidgetJSON($psJSON, $psBlogID) - Deprecated</h4>
        <p>This will probabily soon merge into function above</p>

        <h4>canWrite($blogid)</h4>
        <p>Check if user has permission to write to this blog</p>

        <h4>addFavourite($pUserID, $pBlogID)</h4>
        <p>Add blog to the users favourites list - this is called using ajax from the blog view</p>

        <h4>removeFavourite($pUserID, $pBlogID)</h4>
        <p>Remove blog to the users favourites list - this is called using ajax from the blog view</p>

        <h4>isFavourite($pUserID, $pBlogID)</h4>
        <p>Check if $blog is in $users favourites list - this is called using ajax from the blog view</p>

        <h4>getAllFavourites($pUserID)</h4>
        <p>Get all favorite blogs for $user</p>

        <h4>getAllFavouritesByBlog($pBlogID)</h4>
        <p>Get users which have favourited a $blog</p>

        <h4>getTopFavourites($num=10, $page=0)</h4>
        <p>Get blogs by top favourites</p>

    </div>
        
    <h3>Posts</h3>
    <h3>Comments</h3>
    <h3>Contributors</h3>

</div>