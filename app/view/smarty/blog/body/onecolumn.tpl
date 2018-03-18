<div class="posts">
    <div id="messages">
    {foreach $messages as $message}
        <p class="ui message {$message.type}">{$message.text}</p>
    {/foreach}
    </div>

    {$page_content}

    <div class="actions">
        {include file='blog/actions.tpl'}
    </div>
</div>