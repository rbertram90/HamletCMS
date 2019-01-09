<!DOCTYPE html>
<html>
<head>
    <title>{$page_title} - powered by the Blog CMS from RBwebdesigns</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="HandheldFriendly" content="true">
    
    {$stylesheets}
    {$scripts}
    
    <script>
    // Galleria.loadTheme('{$cms_url}/resources/js/galleria.classic.min.js');
    // Galleria.run('.galleria');
    </script>

    <link rel="stylesheet" href="{$blog->resourcePath()}/default.css?v={'His'|date}" type="text/css">

    <!-- Custom CSS -->
    <style type="text/css" id="customcss">{$custom_css}</style>
    
    {$page_headerbackground}
    
    <script type="text/javascript">
    // this (was) used for addFavourite code - not active
    var folder_root = "/projects/blog_cms";
    </script>
</head>
<body id="blog">
    <div class="ui stackable grid container page-wrapper">
        {* Dynamic header template *}
        {$header_content}
        
        {$columncount = 2}
        {$postcolumn = 1}
        
        {if isset($template_config.Layout)}
            {$columncount = $template_config.Layout.ColumnCount}
        {/if}
        
        {if $columncount == 1}
            {include file='body/onecolumn.tpl'}
        {elseif $columncount == 3}
            {$postcolumn = $template_config.Layout.PostsColumn}
            {if $postcolumn == 1}
                {include file='body/threecolumnsleft.tpl'}
            {elseif $postcolumn == 2}
                {include file='body/threecolumnscentre.tpl'}
            {else}
                {include file='body/threecolumnsright.tpl'}
            {/if}
        {else}
            {$postcolumn = $template_config.Layout.PostsColumn}
            {if $postcolumn == 1}
                {include file='body/twocolumnsleft.tpl'}
            {else}
                {include file='body/twocolumnsright.tpl'}
            {/if}
        {/if}
        
        {* Dynamic footer template *}
        {$footer_content}

        <div class="row page-bookend">
            <div class="sixteen wide column">       
                <div>Powered by Blog CMS from <a href="https://www.rbwebdesigns.co.uk/">rbwebdesigns.co.uk</a></div>
            </div>
        </div>
    </div>
</body>
</html>