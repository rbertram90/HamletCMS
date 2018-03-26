<div class="sixteen wide column posts">
    <div class="messages">
    {foreach $messages as $message}
        <p class="ui message {$message.type}">{$message.text}</p>
    {/foreach}
    </div>

    {$body_content}

    <div class="actions">
        {include file='blog/actions.tpl'}
    </div>
</div>