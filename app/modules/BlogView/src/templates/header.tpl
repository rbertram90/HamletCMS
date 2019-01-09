{*
 * Default blog header template
 * Available variables
 * $blog
 * $hide_title
 * $hide_description
 * $widgets
 * $page_navigation
 *}
<div class="sixteen wide column">
    {if !$hide_title}
        <h1><a href="{$blog->reletivePath()}">{$blog->name}</a></h1>
    {/if}
    
    {if !$hide_description}
        <h2>{$blog->description}</h2>
    {/if}
    
    {if isset($widgets)}
        {$widgets}
    {/if}
    
    {if strlen($page_navigation) > 0}
        <div class="ui menu">{$page_navigation}</div>
    {/if}
</div>