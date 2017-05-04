{viewCrumbtrail(array(), 'Explore')}
{viewPageHeader('Browse Blogs By Letter', 'plane.png')}

<!--Explore Menu-->
<nav class="explore-menu">
	<a href="/explore/popular">Most Popular</a>
	<a href="/explore/blogsbyletter">Browse By Letter</a>
</nav>

<div class="alphabet-navigation">
    
	{foreach from=$alphabet item=character}
    
        {if $counts[$character] > 0}
            <a href="/explore/blogsbyletter/{$character}">{$character}</a>
        {else}
            <span class="nolinkletter">{$character}</span>
        {/if}
    
	{/foreach}
    
</div>

{if isset($blogs)}

	<div class="post-display-one">
        <h3>Blogs Beginning with {$letter}</h3>
	
	{if count($blogs) == 0}
        
        <p>No Blogs have been created beginning with letter {$letter}<p>
        
    {else}
        
        {foreach from=$blogs item=blog}
            <div class='blog-link'>
                <a href='/blogs/{$blog.id}'>{$blog.name}</a>
            </div>
        {/foreach}
        
	{/if}

    </div>

{/if}