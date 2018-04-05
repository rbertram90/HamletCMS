<?php
/* Smarty version 3.1.31, created on 2018-04-05 23:06:56
  from "C:\xampp_5.6.24\htdocs\rbwebdesigns\projects\blog_cms\app\view\smarty\index.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5ac69e00e7c5f2_92263249',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1e7142a19bc9ef2457b796d86ea9dfb4c5be0d89' => 
    array (
      0 => 'C:\\xampp_5.6.24\\htdocs\\rbwebdesigns\\projects\\blog_cms\\app\\view\\smarty\\index.tpl',
      1 => 1522966016,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ac69e00e7c5f2_92263249 (Smarty_Internal_Template $_smarty_tpl) {
echo viewPageHeader('Your Blogs','book.png');?>



<?php if (count($_smarty_tpl->tpl_vars['blogs']->value) > 0) {?>

<table class="ui padded table">
    <thead>
        <tr>
        <th>Blog Name</th>
        <th>Contributors</th>
        <th class="collapsing"></th>
        <th class="collapsing"></th>
        </tr>
    </thead>
    <tbody>
        
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['blogs']->value, 'blog');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['blog']->value) {
?>
            <tr>
                <td>
                    <a href="/cms/blog/overview/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['blog']->value['name'];?>
" style="font-size:120%;"><?php echo $_smarty_tpl->tpl_vars['blog']->value['name'];?>
</a>
                    <br><span class="date"><?php echo $_smarty_tpl->tpl_vars['blog']->value['latestpost']['timestamp'];?>
</span>
                </td>
                <td>
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['blog']->value['contributors'], 'contributor', false, NULL, 'contributors', array (
  'last' => true,
  'iteration' => true,
  'total' => true,
));
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['contributor']->value) {
$_smarty_tpl->tpl_vars['__smarty_foreach_contributors']->value['iteration']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_contributors']->value['last'] = $_smarty_tpl->tpl_vars['__smarty_foreach_contributors']->value['iteration'] == $_smarty_tpl->tpl_vars['__smarty_foreach_contributors']->value['total'];
?>
                        
                        <a href="/cms/account/user/<?php echo $_smarty_tpl->tpl_vars['contributor']->value['id'];?>
" class="user-link">
                        
                        <?php if ($_smarty_tpl->tpl_vars['contributor']->value['id'] == $_SESSION['user']) {?><span data-userid="<?php echo $_smarty_tpl->tpl_vars['contributor']->value['id'];?>
">You</span><?php } else { ?><span data-userid="<?php echo $_smarty_tpl->tpl_vars['contributor']->value['id'];?>
"><?php echo $_smarty_tpl->tpl_vars['contributor']->value['username'];?>
</span><?php }
if (!(isset($_smarty_tpl->tpl_vars['__smarty_foreach_contributors']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_contributors']->value['last'] : null)) {?>,<?php }?>
                        </a>
                        
                    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>


                    <?php echo '<script'; ?>
>
                      $(".user-link").mouseenter(function() {showUserProfile($(this), "<?php echo @constant('CLIENT_ROOT_ABS');?>
", "<?php echo $_smarty_tpl->tpl_vars['clientroot_blogcms']->value;?>
")});
                      $(".user-link").mouseleave(function() {hideUserProfile($(this))});
                    <?php echo '</script'; ?>
>
                </td>
                <td>
                    <div class="ui compact menu">
                        <div class="ui simple dropdown item single line blue">
                            - Actions -
                            <i class="dropdown icon"></i>
                            <div class="menu">
                                <a href="/cms/posts/manage/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
" class="item">Manage Current Posts</a>
                                <a href="/cms/posts/create/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
" class="item">Create New Post</a>
                                <a href="/cms/contributors/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
" class="item">Contributors</a>
                                <a href="/cms/settings/menu/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
" class="item">Blog Settings</a>
                                <a href="/cms/files/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
" class="item">Files</a>
                            </div>
                        </div>
                    </div>
                </td>
                <td>
                    <a href="/blogs/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
" class="ui button teal single line" target="_blank">View Blog</a>
                </td>
            </div>

        <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

    </tbody>
</table>


<?php } else { ?>

    <p class="ui message info">You're not contributing to any blogs, why not <a href="/cms/blog/create">create your first blog</a>?</p>

<?php }?>


<?php }
}
