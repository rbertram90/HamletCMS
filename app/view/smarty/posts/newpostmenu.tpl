{* New Post Menu *}
    
<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/cms/blog/overview/{$blog['id']}", "{$blog['name']}"), 'Create New Post')}
        </div>
    </div>

    <div class="one column row">
        <div class="column">
            {viewPageHeader('New Post', 'doc_add.png', $blog.name)}
        </div>
    </div>


    <div class="two columns row">
        
        <div class="column">
            <div class="ui segment clearing">
                <img src="/resources/icons/64/pages.png" class="ui left floated image">
                <p><a href="/cms/posts/create/{$blog.id}/standard">Markdown Post</a></p>
                <p>Create a post using markdown code to add formatting.</p>
            </div>
            <div class="ui segment clearing">
                <img src="/resources/icons/64/pages.png" class="ui left floated image">
                <p><a href="/cms/posts/create/{$blog.id}/layout">Layout Post</a></p>
                <p>Create a post using a layout editor to create a structured post.</p>
            </div>
            <!--
            <div class="ui segment clearing">
                <img src="/resources/icons/64/camera2.png" class="ui left floated image">
                <p><a href="/cms/posts/create/{$blog.id}/gallery">Gallery Post</a></p>        
                <p>Add multiple images in a gallery to your blog.</p>
            </div>
            -->
        </div>
        
        <!--
        <div class="column">
            <div class="ui segment clearing">
                <img src="/resources/icons/64/film.png" class="ui left floated image">
                <p><a href="/cms/posts/create/{$blog.id}/video">Video Post</a></p>
                <p>Feature a video in your blog post can still add text and title.</p>
            </div>
        </div>
        -->
        
    </div>
            
</div>

<style>.ui.segment img.floated { margin-bottom:0px; width:44px; }</style>