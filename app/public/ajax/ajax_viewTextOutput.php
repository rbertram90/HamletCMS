<?php
    //include_once 'ajax_setup.inc.php';
    require_once dirname(__FILE__).'/../../root.inc.php';
    
/*
    include_once SERVER_ROOT.'/core/core-functions.php';

    include_once SERVER_ROOT.'/core/sanitize.php';
    include_once SERVER_ROOT.'/core/wiki.php';


    // Get the raw content that was sent by the user and make it safe
    $content = sanitizeWikiMarkup($_GET['content']);

    // Convert all tags to from wiki to HTML
    $content = wikiToHTML($content);
*/

    // Setup for 'plugins' Installed using composer
    require_once SERVER_ROOT.'/vendor/autoload.php';
    
    // Parse markdown from ajax request
    // $markdown = new Markdown();
    
    // $htmlmarkup = $markdown->transform($_GET['content']);
    
    use \Michelf\Markdown;
    $htmlmarkup = Markdown::defaultTransform($_GET['content']);
    
    // Return as response
    echo $htmlmarkup;
?>