<div class="four wide column widgets-left">
    {include file='actions.tpl'}
    {if isset($widgets.leftpanel)}
        {$widgets.leftpanel}
    {/if}

</div>
<div class="four wide column widgets-right">
    {if isset($widgets.rightpanel)}
        {$widgets.rightpanel}
    {/if}

</div>
<div class="eight wide column posts">
    {if count($messages) > 0}
        <div class="messages">
        {foreach $messages as $message}
            <p class="ui message {$message.type}">{$message.text}</p>
        {/foreach}
        </div>
    {/if}

    {$body_content}
</div>