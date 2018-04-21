<!DOCTYPE html>
<html>
<head>
    <title>{$page_title} - powered by the Blog CMS by RBwebdesigns</title>
    <meta charset="UTF-8">
    
    {$stylesheets}
    {$scripts}
    
    <script>
    Galleria.loadTheme('/resources/js/galleria.classic.min.js');
    // Galleria.run('.galleria');
    </script>

    <link rel="stylesheet" href="/blogdata/{$blog.id}/default.css" type="text/css">

    <!-- Custom CSS -->
    <style type="text/css" id="customcss">{$custom_css}</style>
    
    {$page_headerbackground}
    
    <script type="text/javascript">var folder_root = "/projects/blog_cms";</script>
</head>
<body>
    <div class="ui grid container page-wrapper">
        <div class="row">
            <div class="sixteen wide column page-header">

                {if !$header_hide_title}
                    <h1><a href="/blogs/{$blog.id}">{$blog.name}</a></h1>
                {/if}
                
                {if !$header_hide_description}
                    <h2>{$blog.description}</h2>
                {/if}
                
                {if isset($widget_content.header)}
                    {$widget_content.header}
                {/if}
                
                <div class="navigation">{$page_navigation}</div>

            </div>
        </div>
        
        {$columncount = 2}
        {$postcolumn = 1}
        
        {if isset($template_config.Layout)}
            {$columncount = $template_config.Layout.ColumnCount}
            {$postcolumn = $template_config.Layout.PostsColumn}
        {/if}
        
        {if $columncount == 1}
            {include file='blog/body/onecolumn.tpl'}
        {elseif $columncount == 3}
            {if $postcolumn == 1}
                {include file='blog/body/threecolumnsleft.tpl'}
            {elseif $postcolumn == 2}
                {include file='blog/body/threecolumnscentre.tpl'}
            {else}
                {include file='blog/body/threecolumnsright.tpl'}
            {/if}
        {else}
            {if $postcolumn == 1}
                {include file='blog/body/twocolumnsleft.tpl'}
            {else}
                {include file='blog/body/twocolumnsright.tpl'}
            {/if}
        {/if}

        <div class="row">
            <div class="sixteen wide column page-footer">

                {if isset($widget_content.footer)}
                    {$widget_content.footer}
                {/if}

                <div class="custom_footer_content">{$page_footercontent}</div>
                Powered by Blog CMS from <a href="http://www.rbwebdesigns.co.uk/">rbwebdesigns.co.uk</a>
            </div>
        </div>
    </div>
</body>
</html>