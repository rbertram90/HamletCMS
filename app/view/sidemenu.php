<?php
/**
 * getCMSSideMenu
 * 
 * @param int $blogid
 *   ID for a blog record to match database
 * 
 * @return string
 *   HTML to show in side menu with links specific to the blog
 * 
 * @todo not show the settings menu option to users who do not have permission to perform the actions
 */
function getCMSSideMenu($blogid, $admin=false)
{
    $output = "<li class='nolink'><span class='menuitemtext'>Blog Actions</span></li>
        <li><a href='/blog/overview/{$blogid}'><img src='/resources/icons/64/bargraph.png'><span class='menuitemtext'>Dashboard</span></a></li>
        <li><a href='/posts/manage/{$blogid}'><img src='/resources/icons/64/papers.png'><span class='menuitemtext'>Manage Posts</span></a></li>
        <li><a href='/comments/all/{$blogid}'><img src='/resources/icons/64/comment.png'><span class='menuitemtext'>Comments</span></a></li>
        <li><a href='/posts/create/{$blogid}'><img src='/resources/icons/64/doc_add.png'><span class='menuitemtext'>Create New Post</span></a></li>
        <li><a href='/files/manage/{$blogid}'><img src='/resources/icons/64/landscape.png'><span class='menuitemtext'>Files</span></a></li>";

    if($admin)
        $output.= "<li><a href='/settings/menu/{$blogid}'><img src='/resources/icons/64/gear.png'><span class='menuitemtext'>Settings</span></a></li>
        <li><a href='/contributors/{$blogid}'><img src='/resources/icons/64/friends.png'><span class='menuitemtext'>Contributors</span></a></li>";

    $output.= "<li><a href='/blogs/{$blogid}'><img src='/resources/icons/64/bargraph.png'><span class='menuitemtext'>View Blog</span></a></li>";
    
    return $output;
}
