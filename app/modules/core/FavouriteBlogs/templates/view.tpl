<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewPageHeader('My Favourites', 'heart')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            <h2>Blogs ({count($favouriteBlogs)})</h2>
            <div class="ui link cards">
            {foreach $favouriteBlogs as $favourite}
                {$blog = $favourite->blog()}
                <div class="card">
                    {if $blog->logo}
                        <a href="{$blog->url()}" class="image">
                            <img src="/blogdata/{$blog->id}/{$blog->logo}" alt="">
                        </a>
                    {/if}        
                    <div class="content">
                        <a href="{$blog->url()}" class="header">{$blog->name}</a>
                        <div class="meta">
                            <span>{$blog->category}</span>
                        </div>
                        <div class="description">
                            {$blog->description}
                        </div>
                    </div>
                    {if $blog->posts()}
                        <div class="extra content">
                            <span class="right floated">
                                Last post: {$blog->latestPost()->timestamp|date_format}
                            </span>
                            <span>
                                <i class="book icon"></i>
                                {count($blog->posts())} posts
                            </span>
                        </div>
                    {/if}
                    <a href="/blogs/{$blog->id}/favourite/remove" class="ui bottom attached button">
                        <i class="minus icon"></i>
                        Remove from favourites
                    </a>
                </div>
            {/foreach}
            </div>
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            <h2>Posts ({count($favouritePosts)})</h2>
            <div class="ui divided items">
            {foreach $favouritePosts as $favourite}
                {$post = $favourite->post()}
                <div class="item">
                    {if $post->teaser_image != ''}
                        <a href="{$post->url()}" class="image">
                            <img src="/blogdata/{$post->blog()->id}/images/s/{$post->teaser_image}" alt="">
                        </a>
                    {/if}
                    <div class="content">
                        <a href="{$post->url()}" class="header">{$post->title}</a>
                        <div class="meta">
                            <span class="cinema">{$post->summary}</span>
                        </div>
                        <div class="description">
                            <p></p>
                        </div>
                        <div class="extra">
                            {$post->author()->name} {$post->author()->surname}, {$post->timestamp|date_format}
                        </div>
                    </div>
                </div>
            {/foreach}
            </div>
        </div>
    </div>
</div>