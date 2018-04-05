<?php
/* Smarty version 3.1.31, created on 2018-04-05 23:09:35
  from "C:\xampp_5.6.24\htdocs\rbwebdesigns\projects\blog_cms\app\view\smarty\account\viewuser.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5ac69e9f72c641_50710342',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '40c159b50d6e582c5b96539ed37d8374fecce54a' => 
    array (
      0 => 'C:\\xampp_5.6.24\\htdocs\\rbwebdesigns\\projects\\blog_cms\\app\\view\\smarty\\account\\viewuser.tpl',
      1 => 1489333581,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ac69e9f72c641_50710342 (Smarty_Internal_Template $_smarty_tpl) {
?>
<h1><?php echo $_smarty_tpl->tpl_vars['user']->value['name'];?>
 <?php echo $_smarty_tpl->tpl_vars['user']->value['surname'];?>
 (<?php echo $_smarty_tpl->tpl_vars['user']->value['username'];?>
)</h1>
<img src="/avatars/thumbs/<?php echo $_smarty_tpl->tpl_vars['user']->value['profile_picture'];?>
" alt="Profile picture" />
<ul>
    <li><strong>Birthday</strong>: <?php echo date('F jS',strtotime($_smarty_tpl->tpl_vars['user']->value['dob']));?>
</li>
    <li><strong>Location</strong>: <?php echo $_smarty_tpl->tpl_vars['user']->value['location'];?>
</li>
</ul><?php }
}
