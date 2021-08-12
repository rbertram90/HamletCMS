<!DOCTYPE html>
<html>
<head>
    <title>{$page_title} - powered by HamletCMS</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,maximum-scale=1,user-scalable=no">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="HandheldFriendly" content="true">
    {if strlen($blog->icon)}
        <link rel="icon" type="image" href="{$blog->resourcePath()}/{$blog->icon}">
    {/if}
    
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
        
        <div class="row posts-outer">
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
        </div>
        
        {* Dynamic footer template *}
        {$footer_content}

        <div class="row page-bookend">
            <div class="sixteen wide column">
                <div>
                  Powered by <a href="https://github.com/rbertram90/HamletCMS">HamletCMS</a> from
                  <a href="https://www.rbwebdesigns.co.uk/">rbwebdesigns.co.uk</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>