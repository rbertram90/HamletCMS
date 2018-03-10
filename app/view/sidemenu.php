<?php
function getCMSSideMenu($blogid, $p) {
        /*
        $menu = array();
        $menu[] = array('','Your Blogs');
        $menu[] = array('explore', 'Explore Blogs');
        $menu[] = array('changelog', 'Blog CMS Changes');
        if($_SESSION['admin'] == 1) $menu[] = array('developer', 'Developer Docs');    
        $menu[] = array('overview/'.$blogid, 'Overview');
        $menu[] = array('comments/'.$blogid, 'Comments');
        $menu[] = array('posts/'.$blogid, 'Manage Posts');
        $menu[] = array('posts/'.$blogid.'/new', 'Create New Post');
        $menu[] = array('config/'.$blogid, 'Settings');
        $menu[] = array('config/'.$blogid, 'Settings');
        */
    
    
    // (15 June 2015) note: this is a bit of a work-around... we shouldn't be outputting html here...

    // Todo: not show the settings menu option to users who do not have permission to perform the actions

return <<<EOD
    <li class="nolink"><span class='menuitemtext'>Blog Actions</span></li>
    <li><a href="/overview/{$blogid}"><img src='/resources/icons/64/bargraph.png'><span class='menuitemtext'>Dashboard</span></a></li>
    <li><a href="/posts/{$blogid}"><img src='/resources/icons/64/papers.png'><span class='menuitemtext'>Manage Posts</span></a></li>
    <li><a href="/comments/{$blogid}"><img src='/resources/icons/64/comment.png'><span class='menuitemtext'>Comments</span></a></li>
    <li><a href="/posts/{$blogid}/new"><img src='/resources/icons/64/doc_add.png'><span class='menuitemtext'>Create New Post</span></a></li>
    <li><a href="/files/{$blogid}"><img src='/resources/icons/64/landscape.png'><span class='menuitemtext'>Files</span></a></li>
    <li><a href="/config/{$blogid}"><img src='/resources/icons/64/gear.png'><span class='menuitemtext'>Settings</span></a></li>
    <li><a href="/contributors/{$blogid}"><img src='/resources/icons/64/friends.png'><span class='menuitemtext'>Contributors</span></a></li>
    <li><a href="/blogs/{$blogid}"><img src='/resources/icons/64/bargraph.png'><span class='menuitemtext'>View Blog</span></a></li>
EOD;
    
}
?>