<div class="ten wide column posts">
    {if count($messages) > 0}
        <div class="messages">
        {foreach $messages as $message}
            <p class="ui message {$message.type}">{$message.text}</p>
        {/foreach}
        </div>
    {/if}

    {$body_content}
</div>
<div class="six wide column widgets-right">
    {include file='blog/actions.tpl'}
    {if isset($widgets.rightpanel)}
        {$widgets.rightpanel}
    {/if}
</div>