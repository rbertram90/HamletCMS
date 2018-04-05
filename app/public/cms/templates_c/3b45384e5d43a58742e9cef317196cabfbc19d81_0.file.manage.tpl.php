<?php
/* Smarty version 3.1.31, created on 2018-04-05 23:09:19
  from "C:\xampp_5.6.24\htdocs\rbwebdesigns\projects\blog_cms\app\view\smarty\contributors\manage.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5ac69e8f7b87b6_80690536',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3b45384e5d43a58742e9cef317196cabfbc19d81' => 
    array (
      0 => 'C:\\xampp_5.6.24\\htdocs\\rbwebdesigns\\projects\\blog_cms\\app\\view\\smarty\\contributors\\manage.tpl',
      1 => 1522958838,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ac69e8f7b87b6_80690536 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_date_format')) require_once 'C:\\xampp_5.6.24\\htdocs\\rbwebdesigns\\projects\\blog_cms\\app\\vendor\\smarty\\smarty\\libs\\plugins\\modifier.date_format.php';
?>
<div class="ui grid">
    <div class="row">
        <div class="column">
            <?php echo viewCrumbtrail(array("/cms/blog/overview/".((string)$_smarty_tpl->tpl_vars['blog']->value['id']),((string)$_smarty_tpl->tpl_vars['blog']->value['name'])),'Contributors');?>

        </div>
    </div>
    <div class="row">
        <div class="column">
            <?php echo viewPageHeader('Contributors','friends.png',((string)$_smarty_tpl->tpl_vars['blog']->value['name']));?>

        </div>
    </div>
    <div class="row">
        <div class="column">
            <h2>Groups</h2>
            <p class="ui message">Groups allow you to define the set of actions that a contributor can perform on a granular level</p>
            <div class="ui segments">
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['groups']->value, 'group');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['group']->value) {
?>
                    <div class="ui segment">
                        <div class="ui small header">
                            <?php if ($_smarty_tpl->tpl_vars['group']->value['locked'] == 0) {?>
                                <a href="/cms/contributors/editgroup/<?php echo $_smarty_tpl->tpl_vars['group']->value['id'];?>
" class="ui right floated button">Edit Permissions</a>
                            <?php }?>
                            <div class="content"><?php echo $_smarty_tpl->tpl_vars['group']->value['name'];?>
</div>
                            <div class="sub header"><?php echo $_smarty_tpl->tpl_vars['group']->value['description'];?>
</div>
                        </div>
                    </div>
                <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

            </div>

            <div class="ui hidden divider"></div>

            <a href="/cms/contributors/creategroup/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
" class="ui teal button">Add Group</a>
        </div>
    </div>
    <div class="row">
        <div class="column">
            <h2>Contributors</h2>
            <div class="ui five cards contributors-list">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['contributors']->value, 'contributor');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['contributor']->value) {
?>
                <div class="card">
                    <div class="image">
                        <?php ob_start();
echo $_smarty_tpl->tpl_vars['contributor']->value['profile_picture'];
$_prefixVariable1=ob_get_clean();
ob_start();
echo $_smarty_tpl->tpl_vars['contributor']->value['profile_picture'];
$_prefixVariable2=ob_get_clean();
if (strlen($_prefixVariable1) > 0 && trim($_prefixVariable2) != "profile_default.jpg") {?>
                            <img src="/avatars/thumbs/<?php echo $_smarty_tpl->tpl_vars['contributor']->value['profile_picture'];?>
">
                        <?php } elseif ($_smarty_tpl->tpl_vars['contributor']->value['gender'] == 'Female') {?>
                            <img src="/avatars/default_woman.png">
                        <?php } else { ?>
                            <img src="/avatars/default_man.png">
                        <?php }?>
                    </div>

                    <div class="content">
                        <div class="header">
                            <a href="/cms/account/user/<?php echo $_smarty_tpl->tpl_vars['contributor']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['contributor']->value['name'];?>
 <?php echo $_smarty_tpl->tpl_vars['contributor']->value['surname'];?>
</a>
                            <?php if ($_smarty_tpl->tpl_vars['blog']->value['user_id'] == $_smarty_tpl->tpl_vars['contributor']->value['id']) {?>(owner)<?php }?>
                        </div>
                        <div class="meta">
                            <?php echo $_smarty_tpl->tpl_vars['contributor']->value['groupname'];?>

                        </div>
                        <div class="description">
                            <?php echo $_smarty_tpl->tpl_vars['contributor']->value['description'];?>

                        </div>
                    </div>
                    <div class="extra content">
                        <span class="right floated">
                            <?php if ($_smarty_tpl->tpl_vars['blog']->value['user_id'] != $_smarty_tpl->tpl_vars['contributor']->value['id']) {?>
                                <a href="/cms/contributors/edit/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
/<?php echo $_smarty_tpl->tpl_vars['contributor']->value['id'];?>
"><i class="edit icon"></i></a>
                                <a href="/cms/contributors/remove/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
/<?php echo $_smarty_tpl->tpl_vars['contributor']->value['id'];?>
" onclick="return confirm('Are you sure you want to remove this contributor from the blog?');"><i class="delete icon"></i></a>
                            <?php }?>
                        </span>
                        <span>
                            Joined in <?php echo smarty_modifier_date_format($_smarty_tpl->tpl_vars['contributor']->value['signup_date'],'%b %Y');?>

                        </span>
                    </div>
                </div>
            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

            </div>

            <div class="ui hidden divider"></div>

            <a href="/cms/contributors/create/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
" class="ui teal button">Add Contributor</a>
        </div>
    </div>
</div>
<?php }
}
