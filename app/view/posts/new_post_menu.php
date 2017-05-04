<!--
I will be adding new types of post - youtube and gallery so that videos and images can be added to the blog in a more standardised way.

Note - vimeo also supports embedding... (?)
-->

<?php
function showCreatePostMenu($arrayBlog) {

    $blogcms_root = CLIENT_ROOT_BLOGCMS;
    $cf_root = CLIENT_ROOT;

    echo viewCrumbtrail(array('/overview/'.$arrayBlog['id'], $arrayBlog['name']), 'Create New Post');
    echo viewPageHeader('New Post', 'doc_add.png', $arrayBlog['name']);
    
echo <<<EOD

    <div class="menu-wrapper">
    
        <a href="{$blogcms_root}/posts/{$arrayBlog['id']}/new/standard">
        <img src="{$cf_root}/resources/icons/64/pages.png"/>Standard Post
        <br><span class="subtext">Add text and images in a normal blog style editor

        </a><a href="{$blogcms_root}/posts/{$arrayBlog['id']}/new/video">
        <img src="{$cf_root}/resources/icons/64/film.png"/>Video Post
        <br><span class="subtext">Feature a video in your blog post can still add text and title.

        </a><a href="">
        <img src="{$cf_root}/resources/icons/64/camera2.png"/>Gallery Post <i>(Coming Soon)</i><br>
        <span class="subtext">Add multiple images in a gallery to your blog.</span></a>
        </a>
    </div>

EOD;

}
?>