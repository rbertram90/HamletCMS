<!DOCTYPE html>
<html>
<head>
    <title>{$page_title} - Blog CMS from RBwebdesigns</title>
    <link rel="shortcut icon" href="/images/favicon.png" type="image/png" />
    
    <meta charset="UTF-8"> 
    <meta name="description" content="{$page_description}">
    
    <script type="text/javascript">
        function refreshPage() {
            setTimeout("location.reload(true);",1000);
        }
    </script>
    
    {$stylesheets}
    {$scripts}
</head>
<body>

    <div class="ui stackable two column grid">
        <div class="four wide tablet three wide computer column">
            <div class="ui center aligned inverted teal segment">
                <img src="/images/logo.png" alt="Blog CMS" class="logo">
            </div>

            <nav class="ui fluid vertical pointing menu">
                {foreach from=$page_sidemenu->getLinks() item=link}
                    {if isset($link->url)}
                        {if $link->active}
                            {$active = 'active'}
                        {else}
                            {$active = ''}
                        {/if}
                        <a href="{$link->url}" class="{$active} teal item" target="{$link->target}"><span class="left floated"><i class="{$link->icon} icon"></i></span>{$link->text}</a>
                    {else}
                        <div class="header item">{$link->text}</div>
                    {/if}
                {/foreach}
            </nav>
        </div>
        <div class="twelve wide tablet thirteen wide computer column">

            {if count($messages) > 0}
                <div id="messages">
                    {foreach from=$messages item=$message}                
                        <p class="ui message {$message.type}">{$message.text}</p>
                    {/foreach}
                </div>
            {/if}
            
            {* Output main content *}
            {$body_content}
        </div>
    </div>
</body>
</html>