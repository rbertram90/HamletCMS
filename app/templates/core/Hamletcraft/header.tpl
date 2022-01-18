{*
 * Default blog header template
 * Available variables
 * $blog
 * $widgets
 * $page_navigation
 *}
<div class="row page-header">
    <div class="sixteen wide column">
        <h1><a href="{$blog->relativePath()}">{$blog->name}</a></h1>
        <h2>{$blog->description}</h2>
        
        {if isset($widgets.header)}
            {$widgets.header}
        {/if}
        
        {if isset($widgets.header)}
            {$widgets.header}
        {/if}
    </div>
</div>
{$config = $blog->config()}
{$header_image = $config.header.background_image}
<div class="row page-header-image" style="background-image:url({$header_image});">
</div>