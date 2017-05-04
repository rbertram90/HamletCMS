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
	
    {* Add tag for each stylesheet added *}
    {foreach from=$stylesheets item=stylesheet}
        <link rel="stylesheet" type="text/css" href="{$stylesheet}.css" />
    {/foreach}
	
    {* Add tag for each javascript file added *}
    {foreach from=$jsscripts item=script}
        <script type="text/javascript" src="{$script}.js"></script>
    {/foreach}
</head>
<body>
    {* Include the rbwebdesigns global header *}
    {include file="$serverroot/app/core/view/page_header.tpl"}
    
    <div id="wrapper">
        
        <div id="sidebar">
            
            <div id="logo-holder">
                <img src="/images/logo.png" />
            </div>
            
            <ul>
                {* todo: apply 'current' class back in *}
                <li><a href="/welcome"><img src='/resources/icons/64/globe.png'><span class='menuitemtext'>Welcome</span></a></li>
                <li><a href="/"><img src='/resources/icons/64/book.png'><span class='menuitemtext'>My Blogs</span></a></li>
                <li><a href="/explore"><img src='/resources/icons/64/plane.png'><span class='menuitemtext'>Explore Blogs</span></a></li>

                {if $smarty.session.admin == 1}
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
                {* Check for flash messages *}
                {if isset($smarty.session.messagetoshow) and $smarty.session.messagetoshow != false}				
					{$smarty.session.messagetoshow}
                {/if}
            </div>
            
            {* Output main content *}
            {$content}
        </div>
    </div>
</body>
</html>