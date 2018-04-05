<?php
/* Smarty version 3.1.31, created on 2018-04-05 23:09:41
  from "C:\xampp_5.6.24\htdocs\rbwebdesigns\projects\blog_cms\app\view\smarty\account\uploadavatar.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5ac69ea5b50654_61557934',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1af6828dd7c12eb1907ed8c8b7bae4e8128e1ad3' => 
    array (
      0 => 'C:\\xampp_5.6.24\\htdocs\\rbwebdesigns\\projects\\blog_cms\\app\\view\\smarty\\account\\uploadavatar.tpl',
      1 => 1522958704,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ac69ea5b50654_61557934 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="ui grid">
    <div class="one column row">
        <div class="column">
            <?php echo viewCrumbtrail(array("/cms/account/user",'Account'),'Change profile photo');?>

        </div>
    </div>
    <div class="one column row">
        <div class="column">
            <?php echo viewPageHeader('Change profile photo','id.png');?>

        </div>
    </div>
</div>

<div class="ui three item menu">
  <a href="/cms/account/settings" class="item">Settings</a>
  <a href="/cms/account/password" class="item">Change Password</a>
  <a href="/cms/account/avatar" class="active item">Upload Avatar</a>
</div>

<p class="ui message info">
Click the browse button to locate a file to use as your profile picture. Rude and offensive pictures may be deleted.
<br><br><b>.jpg</b> images only please!
</p>

<p></p>

<form action='/cms/account/changeprofilephoto' method='POST' enctype='multipart/form-data' class="ui form">
    <div class="field">
        <input type="file" name="avatar" id="file">
    </div>
    <div style="text-align:right; width:100%;">
        <input type='submit' name='fld_submit_uploadphoto' value='Change Avatar' class="ui button teal" />
    </div>
</form><?php }
}
