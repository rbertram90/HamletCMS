<?php
/* Smarty version 3.1.31, created on 2018-04-05 23:09:17
  from "C:\xampp_5.6.24\htdocs\rbwebdesigns\projects\blog_cms\app\view\smarty\settings\general.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5ac69e8d300669_15080283',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'afdc9d4fab54361a9c25ef68c8f958b17865daa0' => 
    array (
      0 => 'C:\\xampp_5.6.24\\htdocs\\rbwebdesigns\\projects\\blog_cms\\app\\view\\smarty\\settings\\general.tpl',
      1 => 1522959060,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ac69e8d300669_15080283 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="ui grid">
    <div class="one column row">
        <div class="column">
            <?php echo viewCrumbtrail(array("/cms/blog/overview/".((string)$_smarty_tpl->tpl_vars['blog']->value['id']),((string)$_smarty_tpl->tpl_vars['blog']->value['name']),"/cms/settings/menu/".((string)$_smarty_tpl->tpl_vars['blog']->value['id']),'Settings'),'General Settings');?>

        </div>
    </div>
    <div class="one column row">
        <div class="column">
            <?php echo viewPageHeader('General Settings','id.png',((string)$_smarty_tpl->tpl_vars['blog']->value['name']));?>


            <form method="POST" class="ui form">
                
                <div class="field">
                    <label for="fld_blogname">Blog Name</label>
                    <input type="text" value="<?php echo $_smarty_tpl->tpl_vars['blog']->value['name'];?>
" name="fld_blogname" />
                </div>
                
                <div class="field">
                    <label for="fld_blogdesc">Description</label>
                    <textarea name="fld_blogdesc"><?php echo $_smarty_tpl->tpl_vars['blog']->value['description'];?>
</textarea>
                </div>
                
                <div class="field">
                    <label for="fld_category">Category</label>
                    <select id="fld_category" name="fld_category" class="semantic-dropdown">
                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['categorylist']->value, 'category');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['category']->value) {
?>
                            <option value="<?php echo $_smarty_tpl->tpl_vars['category']->value;?>
"><?php echo ucfirst($_smarty_tpl->tpl_vars['category']->value);?>
</option>
                        <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
?>

                    </select>
                    <?php echo '<script'; ?>
 type="text/javascript">
                        // Set default
                        $("#fld_category").val("<?php echo $_smarty_tpl->tpl_vars['blog']->value['category'];?>
");
                    <?php echo '</script'; ?>
>
                </div>
                
                <div class="field">
                    <label for="fld_blogsecurity">Who should be able to read your blog?</label>
                    <select id="fld_blogsecurity" name="fld_blogsecurity" class="semantic-dropdown">
                        <option value="anon">Everyone</option>
                        <option value="members">Logged In Members</option>
                        <option value="friends">Your Friends</option>
                        <option value="private">Private (Just You)</option>
                    </select>
                    <?php echo '<script'; ?>
 type="text/javascript">
                        // Set default
                        $("#fld_blogsecurity").val("<?php echo $_smarty_tpl->tpl_vars['blog']->value['visibility'];?>
");
                    <?php echo '</script'; ?>
>

                </div>

                <?php echo '<script'; ?>
>
                    // Apply semantic UI dropdown
                    $(".semantic-dropdown").dropdown();
                <?php echo '</script'; ?>
>

                <input type="submit" class="ui button floated right teal" value="Update" />
                <input type="button" value="Cancel" class="ui button floated right" name="goback" onclick="window.history.back()" />

            </form>
        </div>
    </div>
    
</div><?php }
}
