<?php
    //include_once 'ajax_setup.inc.php';
    include_once dirname(__FILE__).'/../../root.inc.php';
    
    /*
    include_once SERVER_ROOT.'/core/core-functions.php';
    include_once SERVER_ROOT.'/core/sanitize.php';
    include_once SERVER_ROOT.'/core/wiki.php';

    // Get the raw content that was sent by the user
    // Note that we can't use safeString here as we want to convert tags
    $content = $_GET['content'];

    // Convert all tags to from wiki to HTML
    $content = HTMLToWiki($content);
*/

    // Setup for 'plugins' Installed using composer
    require_once SERVER_ROOT.'/vendor/autoload.php';
    
    // Parse html from ajax request
    $markdown = new HTML_To_Markdown($_GET['content']);
    
    // Return as response
    echo $markdown;
?>