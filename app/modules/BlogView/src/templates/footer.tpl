{*
 * Default blog footer template
 * Available variables
 * $blog
 * $widgets
 *}
<div class="row page-footer">
    <div class="sixteen wide column">
        {if isset($widgets)}
            {$widgets}
        {/if}
    </div>
</div>