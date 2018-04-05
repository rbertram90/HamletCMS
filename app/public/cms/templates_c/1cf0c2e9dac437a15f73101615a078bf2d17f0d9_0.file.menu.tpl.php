<?php
/* Smarty version 3.1.31, created on 2018-04-05 23:09:15
  from "C:\xampp_5.6.24\htdocs\rbwebdesigns\projects\blog_cms\app\view\smarty\settings\menu.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5ac69e8b7d5c32_98305038',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1cf0c2e9dac437a15f73101615a078bf2d17f0d9' => 
    array (
      0 => 'C:\\xampp_5.6.24\\htdocs\\rbwebdesigns\\projects\\blog_cms\\app\\view\\smarty\\settings\\menu.tpl',
      1 => 1522959099,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ac69e8b7d5c32_98305038 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="ui grid">
    <div class="one column row">
        <div class="column">
            <?php ob_start();
echo $_smarty_tpl->tpl_vars['blog']->value['name'];
$_prefixVariable1=ob_get_clean();
echo viewCrumbtrail(array("/cms/blog/overview/".((string)$_smarty_tpl->tpl_vars['blog']->value['id']),$_prefixVariable1),'Settings');?>

        </div>
    </div>
    <div class="one column row">
        <div class="column">
            <?php ob_start();
echo $_smarty_tpl->tpl_vars['blog']->value['name'];
$_prefixVariable2=ob_get_clean();
echo viewPageHeader('Settings','gear.png',$_prefixVariable2);?>

            <div class="ui secondary segment">
                This section allows you to change the look and feel of the blog. These can only be changed by blog administrators.</p>
            </div>
            <h3 class="ui header">General</h3>
        </div>
    </div>

    <div class="two columns row">
        
        <div class="column">
            <div class="ui segment clearing">
                <img src="/resources/icons/64/id.png" class="ui left floated image">
                <p><a href="/cms/settings/general/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
">Name &amp; Description</a></p>
                <p>Update the identity of your blog</p>
            </div>
            <div class="ui segment clearing">
                <img src="/resources/icons/64/header.png" class="ui left floated image">
                <p><a href="/cms/settings/header/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
">Header</a></p>
                <p>Settings for your blog header</p>
            </div>
            <div class="ui segment clearing">
                <img src="/resources/icons/64/pages_gear.png" class="ui left floated image">
                <p><a href="/cms/settings/pages/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
">Pages</a></p>
                <p>Add posts to the blog menu</p>
            </div>
        </div>
        
        <div class="column">
            <div class="ui segment clearing">
                <img src="/resources/icons/64/pages_gear.png" class="ui left floated image">
                <p><a href="/cms/settings/posts/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
">Posts</a></p>
                <p>Change how posts are displayed</p>
            </div>
            <div class="ui segment clearing">
                <img src="/resources/icons/64/footer.png" class="ui left floated image">
                <p><a href="/cms/settings/footer/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
">Footer</a></p>
                <p>Settings for your blog footer</p>
            </div>
        </div>
    </div>

    <h3 class="ui header">Design</h3>
    
    <div class="two columns row">
        
        <div class="column">
            <div class="ui segment clearing">
                <img src="/resources/icons/64/paintbrush.png" class="ui left floated image">
                <p><a href="/cms/settings/blogdesigner/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
">Customise Design</a></p>
                <p>Fine tune the look of your blog</p>
            </div>
            <div class="ui segment clearing">
                <img src="/resources/icons/64/star_doc.png" class="ui left floated image">
                <p><a href="/cms/settings/template/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
">Change Template</a></p>
                <p>Choose from our pre-made designs</p>
            </div>
        </div>
        
        <div class="column">
            <div class="ui segment clearing">
                <img src="/resources/icons/64/oven_gear.png" class="ui left floated image">
                <p><a href="/cms/settings/widgets/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
">Configure Widgets</a></p>
                <p>Change what is shown on the sidebar of your blog</p>
            </div>
            <div class="ui segment clearing">
                <img src="/resources/icons/64/css.png" class="ui left floated image">
                <p><a href="/cms/settings/stylesheet/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
">Edit Stylesheet</a></p>
                <p>Ideal for Advanced Users</p>
            </div>
        </div>
        
    </div>
    
    <style>.ui.segment img.floated { margin-bottom:0px; width:44px; }</style>
</div><?php }
}
