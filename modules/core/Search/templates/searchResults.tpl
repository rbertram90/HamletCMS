<div class="search-container">

    <h1>Search</h1>

    <div class="ui segment">
        <form action="{$blog->url()}/search" method="GET" class="ui action left icon fluid input">
            <i class="search icon"></i>
            <input type="text" value="{$searchPhrase}" placeholder="Search ..." name="q">
            <button class="ui teal button">Search</button>
        </form>
    </div>

    {if $searchPhrase}

        <h2>Found {count($searchResults)} result(s) for &ldquo;{$searchPhrase}&rdquo;</h2>

        {foreach $searchResults as $post}
            <div class="ui segment">
                <h3><a href="{$blog->url()}/posts/{$post->link}">{$post->title}</a></h3>
            </div>
        {/foreach}

    {/if}

</div>