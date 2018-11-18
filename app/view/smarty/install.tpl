<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="/css/semantic.css">
    <script src="/resources/js/jquery-3.3.1.min.js"></script>
    <script src="/js/semantic.js"></script>
</head>
<body>
<div class="ui text container">
    <div class="ui padded grid">
        <div class="row">
            <div class="teal center aligned column">
                <img src="/images/logo.png" alt="Blog CMS" />
            </div>
        </div>
        <div class="row">
            <div class="column">

            {if count($messages) > 0}
                <div id="messages">
                    {foreach from=$messages item=$message}                
                        <p class="ui message {$message.type}">{$message.text}</p>
                    {/foreach}
                </div>
            {/if}

            <form method="POST">
                <div class="ui styled fluid accordion">

                    {* first tab *}
                    <div class="title active">
                        <i class="dropdown icon"></i>
                        Environment setup
                    </div>
                    <div class="content active">
                        <div class="transition visible">
                            <div class="ui teal message">Change these variables & more in /app/config/config.json</div>
                            <div class="ui clearing segment">
                                <h4 class="ui teal header">Database</h4>
                                <div class="ui list">
                                    <div class="item">
                                        <div class="content">
                                            <div class="header">Server</div>
                                            {$config.database.server}
                                        </div>
                                    </div>
                                    <div class="item">
                                        <div class="content">
                                            <div class="header">Name</div>
                                            {$config.database.name}
                                        </div>
                                    </div>
                                </div>

                                <h4 class="ui teal header">Environment</h4>
                                <div class="ui list">
                                    <div class="item">
                                        <div class="content">
                                            <div class="header">Canonical domain</div>
                                            {$config.environment.canonical_domain}
                                        </div>
                                    </div>
                                    <div class="item">
                                        <div class="content">
                                            <div class="header">Root directory</div>
                                            {$config.environment.root_directory}
                                        </div>
                                    </div>
                                    <div class="item">
                                        <div class="content">
                                            <div class="header">Timezone</div>
                                            {$config.environment.timezone}
                                        </div>
                                    </div>
                                </div>
                                <button type="button" id="next_1" class="ui right floated right labeled icon teal button">
                                    Next<i class="right chevron icon"></i>
                                </button>
                                <script>
                                    $("#next_1").click(function() {
                                        $('.ui.accordion').accordion('open', 1);
                                    });
                                </script>
                            </div>
                        </div>
                    </div>

                    {* second tab *}
                    <div class="title">
                        <i class="dropdown icon"></i>
                        Modules
                    </div>
                    <div class="content">
                        <div class="ui form transition hidden">
                            <div class="ui teal message">Some modules are locked from editing as the system would not function without them. Optional modules can be uninstalled later.</div>
                            <div class="ui clearing segment">
                                {foreach $modules as $key => $module}
                                    <div class="field">
                                    {if $module.optional}
                                        <div class="ui toggle checkbox">
                                            <input type="checkbox" name="{$key}" id="{$key}">
                                            <label for="{$key}">{$key}</label>
                                        </div>
                                    {else}
                                        <div class="ui toggle checkbox">
                                            <input type="checkbox" name="example" id="example" checked disabled="disabled">
                                            <label for="example">{$key}</label>
                                        </div>
                                    {/if}
                                    </div>
                                {/foreach}

                                <button type="button" id="next_2" class="ui right floated right labeled icon teal button">
                                    Next<i class="right chevron icon"></i>
                                </button>
                                <script>
                                    $("#next_2").click(function() {
                                        $('.ui.accordion').accordion('open', 2);
                                    });
                                </script>
                            </div>
                        </div>
                    </div>

                    {* third tab *}
                    <div class="title">
                        <i class="dropdown icon"></i>
                        Admin account
                    </div>
                    <div class="content">
                        <div class="ui form transition hidden">
                            <div class="ui teal message">This is the initial admin account, further accounts can be created after installation is complete.</div>
                            <div class="ui clearing segment">
                                <h4 class="ui teal dividing header">About You</h4>
                                <div class="two fields">
                                    <div class="field">
                                        <label for="fld_name">First Name</label>
                                        <input type="text" name="fld_name" required>
                                    </div>
                                    <div class="field">
                                        <label for="fld_surname">Surname</label>
                                        <input type="text" name="fld_surname" required>
                                    </div>
                                </div>
                                <div class="field">
                                    <label for="fld_email">Email</label>
                                    <input type="text" name="fld_email" required>
                                </div>
                                <div class="field">
                                    <label for="fld_email_2">Re-type Email</label>
                                    <input type="text" name="fld_email_2" required>
                                </div>

                                <h4 class="ui teal dividing header">Your Account</h4>
                                <div class="field">
                                    <label for="fld_username">Username</label>
                                    <input type="text" name="fld_username" required>
                                </div>
                                <div class="field">
                                    <label for="fld_password">Password</label>
                                    <input type="password" name="fld_password" required>            
                                </div>
                                <div class="field">
                                    <label for="fld_password_2">Re-type Password</label>
                                    <input type="password" name="fld_password_2" required>
                                </div>

                                <button type="submit" class="ui right floated right labeled icon teal button">
                                    Finish<i class="right chevron icon"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
                <script>$('.ui.accordion').accordion();</script>
            </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>