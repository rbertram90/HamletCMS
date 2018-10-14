<!DOCTYPE html>
<html>
<head>
    <title>{$page_title} - powered by the Blog CMS by RBwebdesigns</title>
    <meta charset="UTF-8">
    
    {$stylesheets}
    {$scripts}
    
    <script>
    Galleria.loadTheme('{$cms_url}/resources/js/galleria.classic.min.js');
    // Galleria.run('.galleria');
    </script>

    <link rel="stylesheet" href="{$blog_file_dir}/default.css" type="text/css">

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
                    <h1><a href="{$blog_root_url}">{$blog.name}</a></h1>
                {/if}
                
                {if !$header_hide_description}
                    <h2>{$blog.description}</h2>
                {/if}
                
                {if isset($widgets.header)}
                    {$widgets.header}
                {/if}
                
                {if strlen($page_navigation) > 0}
                    <div class="ui menu">{$page_navigation}</div>
                {/if}
            </div>
        </div>
        
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

        <div class="row">
            <div class="sixteen wide column page-footer">
                {if isset($widgets.footer)}
                    {$widgets.footer}
                {/if}

                <div class="custom_footer_content">{$page_footercontent}</div>
                Powered by Blog CMS from <a href="http://www.rbwebdesigns.co.uk/">rbwebdesigns.co.uk</a>
            </div>
        </div>
    </div>
</body>
</html>