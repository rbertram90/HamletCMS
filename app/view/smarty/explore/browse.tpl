<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array(), 'Explore')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('Browse Blogs By Letter', 'plane.png')}
        </div>
    </div>

    <!--Explore Menu-->
    <div class="one column row">
        <div class="column">
            <div class="ui buttons">
                <a href="/explore/popular" class="ui button">Most Popular</a>
                <a href="/explore/blogsbyletter" class="ui button active">Browse By Letter</a>
                <a href="/explore/category" class="ui button">Category</a>
            </div>
        </div>
    </div>
    
    <div class="one column row">
        <div class="column">
            <div class="ui pagination menu">
                {foreach from=$alphabet item=character}
                    {if $counts[$character] > 0}
                        <a href="/explore/blogsbyletter/{$character}" class="item">{$character}</a>
                    {else}
                        <a class="disabled item">{$character}</a>
                    {/if}
                {/foreach}
            </div>
        </div>
    </div>

    {if isset($blogs)}
    <div class="one column row">
        <div class="column">
            <div class="ui segments">
                <div class="ui segment">
                    <h3>Blogs Beginning with {$letter}</h3>
                </div>
                
                {if count($blogs) == 0}
                    <div class="ui red segment">
                        <p>No Blogs have been created beginning with letter {$letter}<p>
                    </div>
                {else}
                    {foreach from=$blogs item=blog}
                        <div class="ui segment">
                            <a href='/blogs/{$blog.id}'>{$blog.name}</a>
                        </div>
                    {/foreach}
                {/if}

            </div>
        </div>
    </div>
    {/if}
    
</div>