<?php
/* Smarty version 3.1.31, created on 2018-04-05 23:09:11
  from "C:\xampp_5.6.24\htdocs\rbwebdesigns\projects\blog_cms\app\view\smarty\files\manage.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5ac69e87ab2862_89394464',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c49ed3b44b6683ecf53e9b56f25b8896189d1db1' => 
    array (
      0 => 'C:\\xampp_5.6.24\\htdocs\\rbwebdesigns\\projects\\blog_cms\\app\\view\\smarty\\files\\manage.tpl',
      1 => 1522958862,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ac69e87ab2862_89394464 (Smarty_Internal_Template $_smarty_tpl) {
?>

<div class="ui grid">
    <div class="one column row">
        <div class="column">
            <?php ob_start();
echo $_smarty_tpl->tpl_vars['blog']->value['name'];
$_prefixVariable1=ob_get_clean();
echo viewCrumbtrail(array("/cms/blog/overview/".((string)$_smarty_tpl->tpl_vars['blog']->value['id']),$_prefixVariable1),'Files');?>

        </div>
    </div>
    <div class="one column row">
        <div class="column">
            <?php ob_start();
echo $_smarty_tpl->tpl_vars['blog']->value['name'];
$_prefixVariable2=ob_get_clean();
echo viewPageHeader('Files','landscape.png',$_prefixVariable2);?>

        </div>
    </div>
</div>
<?php if (count($_smarty_tpl->tpl_vars['images']->value) == 0) {?>
    <p class="ui message info">No images have been uploaded to this blog</p>
<?php }?>

<style>
    .imageholder {
        width:31%;
        height:120px;
        display:inline-block;
        background-color:#fff;
        margin:1%;
    }
    .imageholder .image {
        background-size:cover;
        width:100%;
        height:100%;
        text-align:center;
        padding-top:80px;
    }
    .imageholder .image button {
        display:none;
    }
    .imageholder .image:hover button {
        display:inline;
    }
    .imageholder p {
        padding:2px;
        margin:0px;
        font-size:0.9em;
    }
</style>

<div>
    <button type="button" onclick="rbrtf_showWindow('/cms/files/viewimagedrop?blogid=<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
')" title="Insert Image"><img src="/resources/icons/document_image_add_32.png" style="width:15px; height:15px;" /> Add Image</button>
    
    <p>Total Space Used = <?php echo $_smarty_tpl->tpl_vars['foldersize']->value;?>
 KB <br> Limit = 50 MB</p>
</div>

<div style="vertical-align:top;">
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['images']->value, 'image');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['image']->value) {
?><div class="imageholder"><div style="background-image:url('/blogdata/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
/images/<?php echo $_smarty_tpl->tpl_vars['image']->value['name'];?>
');" class="image"><form action="/cms/files/delete/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
/<?php echo $_smarty_tpl->tpl_vars['image']->value['file'];?>
" method="POST"><button onclick="return confirm('Are you sure you want to delete this image?');">Delete</button></form></div><p style="border-bottom:1px solid #ccc;">File size: <?php echo $_smarty_tpl->tpl_vars['image']->value['size'];?>
 KB</p><p>Uploaded: <?php echo $_smarty_tpl->tpl_vars['image']->value['date'];?>
</p></div><?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

</div><?php }
}
