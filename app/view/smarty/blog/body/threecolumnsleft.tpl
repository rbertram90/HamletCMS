<div class="posts">
    <div id="messages">   
    {foreach $messages as $message}
        <p class="ui message {$message.type}">{$message.text}</p>
    {/foreach}
    </div>

    {$body_content}
    
</div><div class="leftcolumn">
    <div class="actions">
        {include file='blog/actions.tpl'}
    </div>
    {if isset($widget_content.leftpanel)}
        {$widget_content.leftpanel}
    {/if}

</div><div class="rightcolumn">
    {if isset($widget_content.rightpanel)}
        {$widget_content.rightpanel}
    {/if}
</div>
