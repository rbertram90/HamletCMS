{*
 * Default blog header template
 * 
 * Available variables:
 *  - $blog
 *  - $widgets
 *  - $user
 *  - $user_is_contributor
 *}
<div class="row page-header">
    <div class="sixteen wide column">
        <h1><a href="{$blog->relativePath()}">{$blog->name}</a></h1>        
        <h2>{$blog->description}</h2>

        {if isset($widgets.header)}
            {$widgets.header}
        {/if}
    </div>
</div>