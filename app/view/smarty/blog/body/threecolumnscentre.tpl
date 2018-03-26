<div class="four wide column widgets-left">
    <div class="actions">
        {include file='blog/actions.tpl'}
    </div>
    {if isset($widget_content.leftpanel)}
        {$widget_content.leftpanel}
    {/if}
</div>
<div class="eight wide column posts">

    <div id="messages">
    {foreach $messages as $message}
        <p class="ui message {$message.type}">{$message.text}</p>
    {/foreach}
    </div>

    {$body_content}
</div>
<div class="four wide column widgets-right">
    {if isset($widget_content.rightpanel)}
        {$widget_content.rightpanel}
    {/if}
</div>
