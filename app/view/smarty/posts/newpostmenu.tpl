{* New Post Menu *}
    
<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/overview/{$blog['id']}", "{$blog['name']}"), 'Create New Post')}
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
                <p><a href="/posts/{$blog.id}/new/standard">Standard Post</a></p>
                <p>Add text and images in a normal blog style editor.</p>
            </div>
            <div class="ui segment clearing">
                <img src="/resources/icons/64/camera2.png" class="ui left floated image">
                <p><a href="/posts/{$blog.id}/new/gallery">Gallery Post</a></p>        
                <p>Add multiple images in a gallery to your blog.</p>
            </div>
        </div>
        
        <div class="column">
            <div class="ui segment clearing">
                <img src="/resources/icons/64/film.png" class="ui left floated image">
                <p><a href="/posts/{$blog.id}/new/video">Video Post</a></p>
                <p>Feature a video in your blog post can still add text and title.</p>
            </div>
        </div>
        
    </div>
            
</div>

<style>.ui.segment img.floated { margin-bottom:0px; width:44px; }</style>