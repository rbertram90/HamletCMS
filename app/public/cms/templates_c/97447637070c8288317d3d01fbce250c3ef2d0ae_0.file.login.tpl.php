<?php
/* Smarty version 3.1.31, created on 2018-04-05 21:15:25
  from "C:\xampp_5.6.24\htdocs\rbwebdesigns\projects\blog_cms\app\view\smarty\account\login.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5ac683ddafd427_88008358',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '97447637070c8288317d3d01fbce250c3ef2d0ae' => 
    array (
      0 => 'C:\\xampp_5.6.24\\htdocs\\rbwebdesigns\\projects\\blog_cms\\app\\view\\smarty\\account\\login.tpl',
      1 => 1522958626,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ac683ddafd427_88008358 (Smarty_Internal_Template $_smarty_tpl) {
?>
<!DOCTYPE html>
<html>
    <head>
        <title><?php echo $_smarty_tpl->tpl_vars['page_title']->value;?>
</title>
        <link rel="stylesheet" href="/css/semantic.css" type="text/css">
        <link rel="stylesheet" href="/css/blogs_stylesheet.css" type="text/css">
        <?php echo '<script'; ?>
 src="/js/semantic.js" type="text/javascript"><?php echo '</script'; ?>
>
    </head>
    <body>
        <style>
            body {
                min-width: inherit;
            }
        </style>
        <div id="loginbox">
            <div id="logoholder">
                <img src="/images/logo.png" alt="Blog CMS" />
            </div>
            
            <h1>Welcome</h1>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['messages']->value, 'message');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['message']->value) {
?>
                <div class="ui message <?php echo $_smarty_tpl->tpl_vars['message']->value['type'];?>
"><?php echo $_smarty_tpl->tpl_vars['message']->value['text'];?>
</div>
            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

            
            <form action="/cms/account/login" method="POST" class="ui form">
                <div class="field">
                    <label for="fld_username">Username</label>
                    <input type="text" name="fld_username" value="" required>
                </div>
                
                <div class="field">
                    <label for="fld_password">Password</label>
                    <input type="password" name="fld_password" required>
                </div>

                <a href="/cms/account/register">Register new account</a>
                
                <button class="ui right floated teal button">Login &nbsp;&#10095;</button>
                <div class="clear"></div>
            </form>
            
            
            <?php echo '<script'; ?>
>
            $('.ui.form').form({
                fields: {
                  fld_username : 'empty',
                  fld_password : 'empty'
                }
            });
            <?php echo '</script'; ?>
>
        </div>
    </body>
</html><?php }
}
