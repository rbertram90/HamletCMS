<?php
/* Smarty version 3.1.31, created on 2018-04-05 23:09:48
  from "C:\xampp_5.6.24\htdocs\rbwebdesigns\projects\blog_cms\app\view\smarty\comments.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5ac69eac2eb9a3_89562928',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'cf6844e46740c8fa84088a434c39bb253b713acf' => 
    array (
      0 => 'C:\\xampp_5.6.24\\htdocs\\rbwebdesigns\\projects\\blog_cms\\app\\view\\smarty\\comments.tpl',
      1 => 1522959312,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ac69eac2eb9a3_89562928 (Smarty_Internal_Template $_smarty_tpl) {
?>


<div class="ui grid">
    <div class="one column row">
        <div class="column">
            <?php ob_start();
echo $_smarty_tpl->tpl_vars['blog']->value['name'];
$_prefixVariable1=ob_get_clean();
echo viewCrumbtrail(array("/cms/blog/overview/".((string)$_smarty_tpl->tpl_vars['blog']->value['id']),$_prefixVariable1),'Comments');?>

        </div>
    </div>

    <div class="one column row">
        <div class="column">
            <?php ob_start();
echo $_smarty_tpl->tpl_vars['blog']->value['name'];
$_prefixVariable2=ob_get_clean();
echo viewPageHeader('Comments','comment.png',$_prefixVariable2);?>

        </div>
    </div>
</div>

<p class="info">Total Comments: <strong><?php echo count($_smarty_tpl->tpl_vars['comments']->value);?>
</strong></p>

<?php if (count($_smarty_tpl->tpl_vars['comments']->value) == 0) {?>
    <div class="segment">No comments have been made on your posts</div>
<?php } else { ?>
    <table class="ui table">
        <thead>
            <tr>
                <th>Comment Text</th>
                <th>Date</th>
                <th>User</th>
                <th>Post</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['comments']->value, 'comment');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['comment']->value) {
?><tr><td><?php echo $_smarty_tpl->tpl_vars['comment']->value['message'];?>
</td><td><?php echo formatdate($_smarty_tpl->tpl_vars['comment']->value['timestamp']);?>
</td><td><a href="/cms/account/user/<?php echo $_smarty_tpl->tpl_vars['comment']->value['userid'];?>
"><?php echo $_smarty_tpl->tpl_vars['comment']->value['username'];?>
</a></td><td><a href="/blogs/<?php echo $_smarty_tpl->tpl_vars['comment']->value['blog_id'];?>
/posts/<?php echo $_smarty_tpl->tpl_vars['comment']->value['link'];?>
"><?php echo $_smarty_tpl->tpl_vars['comment']->value['title'];?>
</a></td><td class="single line"><?php if ($_smarty_tpl->tpl_vars['comment']->value['approved'] == 1) {?><div class="ui label green"><i class="icon checkmark"></i> Approved</div><?php } else { ?><div class="ui label yellow">Pending Approval</div><?php }?></td><td class="single line right aligned"><?php if ($_smarty_tpl->tpl_vars['comment']->value['approved'] == 0) {?><button class="ui green button" onclick="if(confirm('Approve this comment?')) {window.location = '/comments/approve/<?php echo $_smarty_tpl->tpl_vars['comment']->value['id'];?>
'}" title="Approve Comment">Approve</button><?php }?><button class="ui button" onclick="if(confirm('Are you sure you wish to delete this comment?')) {window.location = '/comments/delete/<?php echo $_smarty_tpl->tpl_vars['comment']->value['id'];?>
'}" title="Remove Comment">Delete</button></td></tr><?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

    </table>
<?php }
}
}
