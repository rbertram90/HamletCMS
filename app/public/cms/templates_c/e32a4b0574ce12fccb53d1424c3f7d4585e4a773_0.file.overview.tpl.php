<?php
/* Smarty version 3.1.31, created on 2018-04-05 23:10:04
  from "C:\xampp_5.6.24\htdocs\rbwebdesigns\projects\blog_cms\app\view\smarty\overview.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5ac69ebcc99c98_73687608',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e32a4b0574ce12fccb53d1424c3f7d4585e4a773' => 
    array (
      0 => 'C:\\xampp_5.6.24\\htdocs\\rbwebdesigns\\projects\\blog_cms\\app\\view\\smarty\\overview.tpl',
      1 => 1522966202,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ac69ebcc99c98_73687608 (Smarty_Internal_Template $_smarty_tpl) {
?>


<div class="ui grid">
    <div class="one column row">
        <div class="column">
            <?php echo viewCrumbtrail(array(),$_smarty_tpl->tpl_vars['blog']->value['name']);?>

        </div>
    </div>
    <div class="one column row">
        <div class="column">
            <?php echo viewPageHeader(((string)$_smarty_tpl->tpl_vars['blog']->value['name']),'bargraph.png');?>

        </div>
    </div>

    <div class="four column row">
        <div class="center aligned column">
            <div class="ui teal segment">
                <a href="/cms/posts/manage/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
" title="Manage Posts">
                    <span class="ui header huge"><?php echo $_smarty_tpl->tpl_vars['counts']->value['posts'];?>
</span><br><span>Posts</span>
                </a>
            </div>
        </div>
        <div class="center aligned column">
            <div class="ui teal segment">
                <a href="/cms/comments/all/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
" title="View Comments">
                    <span class="ui header huge"><?php echo $_smarty_tpl->tpl_vars['counts']->value['comments'];?>
</span><br><span>Comments</span>
                </a>
            </div>
        </div>
        <div class="center aligned column">
            <div class="ui teal segment">
                <a href="/cms/contributors/manage/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
" title="Manage Contributors">
                    <span class="ui header huge"><?php echo $_smarty_tpl->tpl_vars['counts']->value['contributors'];?>
</span><br><span>Contributors</span>
                </a>
            </div>
        </div>
        <div class="center aligned column">
            <div class="ui teal segment">
                <a href="/cms/posts/manage/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
" title="Manage Posts">
                    <span class="ui header huge"><?php echo $_smarty_tpl->tpl_vars['counts']->value['totalviews'];?>
</span><br><span>Total Post Views</span>
                </a>
            </div>
        </div>
    </div>

    <div class="stackable two column row">
        <div class="column">
            <h3 class="ui header">Latest Posts</h3>
            
            <?php if ($_smarty_tpl->tpl_vars['counts']->value['posts'] > 0) {?>
                <div class="ui segments">
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['posts']->value, 'post');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['post']->value) {
?>
                    <div class="ui segment">
                        <a href='/cms/blogs/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
/posts/<?php echo $_smarty_tpl->tpl_vars['post']->value['link'];?>
'><?php echo $_smarty_tpl->tpl_vars['post']->value['title'];?>
</a>
                        
                        
                        <?php if ($_smarty_tpl->tpl_vars['post']->value['draft'] == 1) {?><i>(draft)</i><?php }?>

                        
                        <?php if ($_smarty_tpl->tpl_vars['post']->value['timestamp'] > date('Y-m-d H:i:s')) {?><i>(scheduled)</i><?php }?>
                        
                        <div class="comment-date">
                            <?php echo formatDate($_smarty_tpl->tpl_vars['post']->value['timestamp']);?>

                        </div>
                        <div class="comment-info">
                            Added by <a href="/account/user/<?php echo $_smarty_tpl->tpl_vars['post']->value['author_id'];?>
"><?php echo $_smarty_tpl->tpl_vars['post']->value['username'];?>
</a>
                        </div>
                    </div>
                <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

                </div>
            <?php } else { ?>
                <p class="ui message info">Nothing has been posted on this blog, why not <a href="/posts/create/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
">make a start</a>?</p>
            <?php }?>
            <a href='/cms/posts/manage/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
' class='ui teal right floated button'>Manage Posts &gt;</a>
            <a href='/cms/posts/create/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
' class='ui basic teal right floated button'>New Post &gt;</a>
        </div>
        <div class="column">
        <h3 class="ui header">Recent Comments</h3>

        <?php if (count($_smarty_tpl->tpl_vars['comments']->value) > 0) {?>
        <div class="ui segments">
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['comments']->value, 'comment');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['comment']->value) {
?>
                <div class="ui segment">
                    &quot;<?php echo $_smarty_tpl->tpl_vars['comment']->value['message'];?>
&quot;
                    <div class="comment-date">
                        <?php echo formatdate($_smarty_tpl->tpl_vars['comment']->value['timestamp']);?>

                    </div>
                    <div class="comment-info">
                        Added by <a href="/cms/account/user/<?php echo $_smarty_tpl->tpl_vars['comment']->value['userid'];?>
"><?php echo $_smarty_tpl->tpl_vars['comment']->value['name'];?>
</a> on <a href="/blogs/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
/posts/<?php echo $_smarty_tpl->tpl_vars['comment']->value['link'];?>
"><?php echo $_smarty_tpl->tpl_vars['comment']->value['title'];?>
</a>
                    </div>
                </div>
            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

        </div>
            <a href='/cms/comments/all/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
' class='ui teal right floated button'>All Comments &gt;</a>

        <?php } else { ?>
            <p class="ui message info">No comments have been made on your posts on this blog :(</p>
        <?php }?>
        </div>
    </div>    
</div><?php }
}
