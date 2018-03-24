<!DOCTYPE html>
<html>
<head>
    <title>{$page_title} - Blog CMS from RBwebdesigns</title>
    <link rel="shortcut icon" href="/resources/icons/64/gear.png" type="image/png" />
    
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
                {$page_sidemenu}

                {*
                <a id="hidesidemenu" onclick="window.hideSideMenu(); return false;" class="item"><span class="left floated"><i class="arrow alternate circle left outline icon"></i></span> Minimise</a>
                <a id="showsidemenu" onclick="window.showSideMenu(); return false;" class="item"><span class="left floated"><i class="arrow alternate circle right outline icon"></i></span> Maximise</a>
                *}
            </nav>
        </div>
        <div class="twelve wide tablet thirteen wide computer column">
            <div id="messages">
                {foreach from=$messages item=$message}                
                    <p class="ui message {$message.type}">{$message.text}</p>
                {/foreach}
            </div>
            
            {* Output main content *}
            {$body_content}
        </div>
    </div>
</body>
</html>