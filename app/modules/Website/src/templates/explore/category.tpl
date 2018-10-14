<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array(), 'Explore')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('Blogs by category', 'plane.png')}
        </div>
    </div>



    <!--Explore Menu-->
    <div class="one column row">
        <div class="column">
            <div class="ui buttons">
                <a href="/explore/popular" class="ui button">Most Popular</a>
                <a href="/explore/blogsbyletter" class="ui button">Browse By Letter</a>
                <a href="/explore/category" class="ui button active">Category</a>
            </div>
        </div>
    </div>

    <div class="one column row">
        <div class="column">
                        
            {foreach from=$categories item=category}
                {if $currentcategory == $category}
                    <a href="/explore/category/{$category}" class="ui teal label">{ucfirst($category)}</a>
                {else}
                    <a href="/explore/category/{$category}" class="ui label">{ucfirst($category)}</a>
                {/if}
            {/foreach}
            
            <h2 class="ui header">{ucfirst($currentcategory)}</h2>
            
            <table class="ui celled padded table">
                <thead>
                <tr>
                    <th>Blog Name</th>
                </tr>
                </thead>
                {foreach from=$blogs item=blog}

                    <tr>
                        <td><a href="/blogs/{$blog.id}">{$blog.name}</a></td>
                    </tr>

                {foreachelse}
                    <tr>
                        <td>No blogs in the category</td>
                    </tr>
                {/foreach}

            </table>
            
        </div>
    </div>
</div>