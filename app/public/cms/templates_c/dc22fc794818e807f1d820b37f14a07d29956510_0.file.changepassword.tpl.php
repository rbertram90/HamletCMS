<?php
/* Smarty version 3.1.31, created on 2018-04-05 23:09:40
  from "C:\xampp_5.6.24\htdocs\rbwebdesigns\projects\blog_cms\app\view\smarty\account\changepassword.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5ac69ea4b9e636_60828935',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'dc22fc794818e807f1d820b37f14a07d29956510' => 
    array (
      0 => 'C:\\xampp_5.6.24\\htdocs\\rbwebdesigns\\projects\\blog_cms\\app\\view\\smarty\\account\\changepassword.tpl',
      1 => 1522958734,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ac69ea4b9e636_60828935 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="ui grid">
    <div class="one column row">
        <div class="column">
            <?php echo viewCrumbtrail(array("/cms/account/user",'Account'),'Change Password');?>

        </div>
    </div>
    <div class="one column row">
        <div class="column">
            <?php echo viewPageHeader('Change Password','lock.png');?>

        </div>
    </div>
</div>

<div class="ui three item menu">
  <a href="/cms/account/settings" class="item">Settings</a>
  <a href="/cms/account/password" class="active item">Change Password</a>
  <a href="/cms/account/avatar" class="item">Upload Avatar</a>
</div>


<form action="/cms/account/password" method="POST" class="ui form">
    <div class="field">
        <label for="fld_password">Current Password</label>
        <input type="password" name="fld_current_password" onkeyup="validate(this,{password:true})" />            
    </div>
    <div class="field">
        <label for="fld_new_password">Create New Password</label>
        <input type="password" name="fld_new_password" />
    </div>
    <div class="field">
        <label for="fld_new_password_2">Re-type New Password</label>
        <input type="password" name="fld_new_password_rpt" />
    </div>

    <div style="text-align:right; width:100%;">
        <input type="submit" name="fld_submit_passwordchange" value="Change Password" class="ui button teal">
    </div>
</form><?php }
}
