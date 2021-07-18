<div class="sixteen wide column posts">
    {if count($messages) > 0}
        <div class="messages">
        {foreach $messages as $message}
            <p class="ui message {$message.type}">{$message.text}</p>
        {/foreach}
        </div>
    {/if}

    {$body_content}

    {include file='actions.tpl'}
</div>