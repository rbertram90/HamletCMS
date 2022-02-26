{*
 * Default post teaser view
 *
 * Variables:
 *   - $post
 *   - $config
 *   - $blog_root_url
 *   - $blog_file_dir
 *   - $shownumcomments
 *   - $showtags
 *   - $showsocialicons
 *   - $userIsContributor
 *}
<div class="ui fluid card post">

    {if $post->teaser_image and $post->teaser_image != "false"}
        <div class="image">
            <div class="teaser-image">
                <a href="{$blog->relativePath()}/posts/{$post->link}">
                    <img src="{$blog->resourcePath()}/images/s/{$post->teaser_image}" class="ui fluid image">
                </a>
            </div>
        </div>
    {/if}
    
    <div class="content">
        <h2 class="header"><a href="{$post->relativePath()}">{$post->title}</a></h2>
        <div class="description post-content">
            {$post->summary}
        </div>
    </div>
    
    <div class="extra content">
        <span class="right floated post-comment-count"><i class="comment icon"></i> {count($post->getComments())} comments</span>
        <span><i class="calendar icon"></i> {$post->timestamp|date_format}</span>
    </div>
        
    
    {if $userIsContributor}
        <div class="extra content">
            <a href="/cms/posts/delete/{$post->id}" onclick="return confirm('Are you sure?');" class="ui black right floated button">Delete</a>
            <a href="/cms/posts/edit/{$post->id}" class="ui black right floated button">Edit</a>
        </div>
    {/if}
</div>