<?php
/* Smarty version 3.1.31, created on 2018-04-05 21:15:33
  from "C:\xampp_5.6.24\htdocs\rbwebdesigns\projects\blog_cms\app\view\smarty\template.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5ac683e5c1e705_22249772',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'd67ce52c74117f313db2f8a623abc93cb9913b39' => 
    array (
      0 => 'C:\\xampp_5.6.24\\htdocs\\rbwebdesigns\\projects\\blog_cms\\app\\view\\smarty\\template.tpl',
      1 => 1521912786,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ac683e5c1e705_22249772 (Smarty_Internal_Template $_smarty_tpl) {
?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $_smarty_tpl->tpl_vars['page_title']->value;?>
 - Blog CMS from RBwebdesigns</title>
    <link rel="shortcut icon" href="/resources/icons/64/gear.png" type="image/png" />
    
    <meta charset="UTF-8"> 
    <meta name="description" content="<?php echo $_smarty_tpl->tpl_vars['page_description']->value;?>
">
    
    <?php echo '<script'; ?>
 type="text/javascript">
        function refreshPage() {
            setTimeout("location.reload(true);",1000);
        }
    <?php echo '</script'; ?>
>
    
    <?php echo $_smarty_tpl->tpl_vars['stylesheets']->value;?>

    <?php echo $_smarty_tpl->tpl_vars['scripts']->value;?>

</head>
<body>

    <div class="ui stackable two column grid">
        <div class="four wide tablet three wide computer column">
            <div class="ui center aligned inverted teal segment">
                <img src="/images/logo.png" alt="Blog CMS" class="logo">
            </div>

            <nav class="ui fluid vertical pointing menu">
                <?php echo $_smarty_tpl->tpl_vars['page_sidemenu']->value;?>


                
            </nav>
        </div>
        <div class="twelve wide tablet thirteen wide computer column">
            <div id="messages">
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['messages']->value, 'message');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['message']->value) {
?>                
                    <p class="ui message <?php echo $_smarty_tpl->tpl_vars['message']->value['type'];?>
"><?php echo $_smarty_tpl->tpl_vars['message']->value['text'];?>
</p>
                <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

            </div>
            
            
            <?php echo $_smarty_tpl->tpl_vars['body_content']->value;?>

        </div>
    </div>
</body>
</html><?php }
}
