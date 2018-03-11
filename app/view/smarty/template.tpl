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
    <div id="wrapper">
        
        <div id="sidebar">
            
            <div id="logo-holder">
                <img src="/images/logo.png" />
            </div>
            
            <ul>
                {* todo: apply 'current' class back in *}
                <li><a href="/"><img src='/resources/icons/64/book.png'><span class='menuitemtext'>My Blogs</span></a></li>
                <li><a href="/explore"><img src='/resources/icons/64/plane.png'><span class='menuitemtext'>Explore Blogs</span></a></li>

                {if $current_user.admin == 1}
                <li><a href="/developer"><img src='/resources/icons/64/bargraph.png'><span class='menuitemtext'>Developer Docs</span></a></li>
                {/if}
                
                {* Add page-specific menu options *}
                {$page_sidemenu}
                <li id="hidesidemenu"><a href="#" onclick="window.hideSideMenu(); return false;"><img src="/resources/icons/64/arrow_left.png"><span class='menuitemtext'>Minimise</span></a></li>
                <li id="showsidemenu"><a href="#" onclick="window.showSideMenu(); return false;"><img src="/resources/icons/64/arrow_right.png"></a></li>
            </ul>
        </div>
        
        <div id="content">
            
            <div id="messages">
                {foreach from=$messages item=$message}                
                    <p class="message {$message.type}">{$message.text}</p>
                {/foreach}
            </div>
            
            {* Output main content *}
            {$body_content}
        </div>
    </div>
</body>
</html>