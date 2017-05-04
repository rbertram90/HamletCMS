<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array(), 'Explore')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('Most Favourited Blogs', 'plane.png')}
        </div>
    </div>



    <!--Explore Menu-->
    <div class="one column row">
        <div class="column">
            <div class="ui buttons">
                <a href="/explore/popular" class="ui button active">Most Popular</a>
                <a href="/explore/blogsbyletter" class="ui button">Browse By Letter</a>
            </div>
        </div>
    </div>
    

    <div class="one column row">
        <div class="column">
            
            <table class="ui celled padded table">
                <thead>
                <tr>
                    <th>Blog Name</th>
                    <th># Favourites</th>
                </tr>
                </thead>
                {foreach from=$topblogs item=blog}

                    <tr>
                        <td><a href="/blogs/{$blog.id}">{$blog.name}</a></td>
                        <td>{$blog.fav_count}</td>
                    </tr>

                {/foreach}

            </table>
            
        </div>
    </div>
</div>