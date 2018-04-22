<div class="ten wide column posts">
    <div id="messages">
    {foreach $messages as $message}
        <p class="ui message {$message.type}">{$message.text}</p>
    {/foreach}
    </div>

    {$body_content}
</div>
<div class="six wide column widgets-right">
    <div class="actions">
        {include file='blog/actions.tpl'}
    </div>
    {if isset($widgets.leftpanel)}
        {$widgets.leftpanel}
    {/if}
</div>