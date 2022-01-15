<!DOCTYPE html>
<html>
<head>
    <title>{$page_title} - HamletCMS</title>
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
<body id="cms">

<div class="ui top fixed menu">
    <a class="item" href="/cms" style="background-color:#00B5AD;" title="CMS home">
        <img src="/images/square_small_logo.png" alt="HamletCMS">
    </a>

    {if count($blogs)}
        <div class="ui dropdown item blog-menu">
            Blogs <i class="dropdown icon"></i>
            <div class="menu">
                {foreach from=$blogs item=blog}
                    <a href="/cms/blog/overview/{$blog->id}" class="item">{$blog->name}</a>
                {/foreach}
            </div>
        </div>
    {/if}
    
    <div class="right menu">
        {if $user->admin}
            <div class="ui dropdown item blog-menu">
                Admin tools <i class="dropdown icon"></i>
                {viewMenu($page_admin_menu)}
            </div>
        {/if}
        <div class="ui dropdown item user-menu" title="Account menu">
            <img src="/avatars/thumbs/{$user->profile_picture}" alt="Profile image" class="user-icon"> {$user->username} <i class="dropdown icon"></i>
            {viewMenu($page_user_menu)}
        </div>
    </div>
</div>
<script>
    $(".user-menu").dropdown();
    $(".blog-menu").dropdown();
</script>

    <div class="ui stackable two column grid cms-body">
        <div class="four wide tablet three wide computer column">
            {viewMenu($page_side_menu, "cms_main_menu", "fluid vertical pointing")}
        </div>
        <div class="twelve wide tablet thirteen wide computer column">            
            {* Output main content *}
            {$body_content}
        </div>
    </div>

    {if count($messages) > 0}
        <div class="messages-wrapper">
            {foreach from=$messages item=$message}
                <div class="status-message notify do-show" data-notification-status="{$message.type}">{$message.text}</div>
            {/foreach}
        </div>
    {/if}

    <script src="/js/messages.js"></script>
</body>
</html>