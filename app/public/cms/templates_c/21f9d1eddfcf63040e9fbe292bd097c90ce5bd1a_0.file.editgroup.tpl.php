<?php
/* Smarty version 3.1.31, created on 2018-04-05 23:09:23
  from "C:\xampp_5.6.24\htdocs\rbwebdesigns\projects\blog_cms\app\view\smarty\contributors\editgroup.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5ac69e93e53c36_09335890',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '21f9d1eddfcf63040e9fbe292bd097c90ce5bd1a' => 
    array (
      0 => 'C:\\xampp_5.6.24\\htdocs\\rbwebdesigns\\projects\\blog_cms\\app\\view\\smarty\\contributors\\editgroup.tpl',
      1 => 1522958805,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ac69e93e53c36_09335890 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="ui grid">
    <div class="row">
        <div class="column">
            <?php echo viewCrumbtrail(array("/cms/blog/overview/".((string)$_smarty_tpl->tpl_vars['blog']->value['id']),((string)$_smarty_tpl->tpl_vars['blog']->value['name']),"/cms/contributors/manage/".((string)$_smarty_tpl->tpl_vars['blog']->value['id']),"Contributors"),'Edit Group');?>

        </div>
    </div>
    <div class="row">
        <div class="column">
            <?php echo viewPageHeader("Edit Group",'friends.png',((string)$_smarty_tpl->tpl_vars['group']->value['name'])." - ".((string)$_smarty_tpl->tpl_vars['blog']->value['name']));?>

        </div>
    </div>
    <div class="row">
        <div class="column">
            <form class="ui form" method="POST">
                <h2>Basic Information</h2>
                <div class="field">
                    <label for="fld_name">Group Name</label>
                    <input type="text" value="<?php echo $_smarty_tpl->tpl_vars['group']->value['name'];?>
" name="fld_name" id="fld_name">
                </div>

                <div class="field">
                    <label for="fld_description">Description</label>
                    <input type="text" value="<?php echo $_smarty_tpl->tpl_vars['group']->value['description'];?>
" name="fld_description" id="fld_description">
                </div>

                <div class="ui divider hidden"></div>

                <h2>Permissions</h2>
                <p class="ui visible warning message">Use these with caution, only give permissions to people you trust!</p>

                <div class="inline field">
                    <div class="ui checkbox">
                        <input class="hidden" id="perm_create_posts" type="checkbox" name="fld_permission[create_posts]">
                        <label>Create posts</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui checkbox">
                        <input class="hidden" id="perm_publish_posts" type="checkbox" name="fld_permission[publish_posts]">
                        <label>Publish posts</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui checkbox">
                        <input class="hidden" id="perm_edit_all_posts" type="checkbox" name="fld_permission[edit_all_posts]">
                        <label>Edit any post</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui checkbox">
                        <input class="hidden" id="perm_delete_posts" type="checkbox" name="fld_permission[delete_posts]">
                        <label>Delete any posts</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui checkbox">
                        <input class="hidden" id="perm_manage_comments" type="checkbox" name="fld_permission[manage_comments]">
                        <label>Manage comments</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui checkbox">
                        <input class="hidden" id="perm_delete_files" type="checkbox" name="fld_permission[delete_files]">
                        <label>Delete files</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui checkbox">
                        <input class="hidden" id="perm_change_settings" type="checkbox" name="fld_permission[change_settings]">
                        <label>Change blog settings</label>
                    </div>
                </div>
                <div class="field">
                    <div class="ui checkbox">
                        <input class="hidden" id="perm_manage_contributors" type="checkbox" name="fld_permission[manage_contributors]">
                        <label>Manage blog contributors</label>
                    </div>
                </div>

                <div class="ui divider hidden"></div>

                <button class="ui button teal">Save</button>
                <button type="button" class="ui button" onclick="window.history.back();">Cancel</button>
            </form>
        </div>
    </div>
</div>

<?php echo '<script'; ?>
>
    $('.ui.checkbox').checkbox();

    // Apply defaults
    <?php if ($_smarty_tpl->tpl_vars['group']->value['permissions']['create_posts']) {?>
        $('#perm_create_posts').attr("checked", "checked");
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['group']->value['permissions']['publish_posts']) {?>
        $('#perm_publish_posts').attr("checked", "checked");
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['group']->value['permissions']['edit_all_posts']) {?>
        $('#perm_edit_all_posts').attr("checked", "checked");
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['group']->value['permissions']['delete_posts']) {?>
        $('#perm_delete_posts').attr("checked", "checked");
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['group']->value['permissions']['manage_comments']) {?>
        $('#perm_manage_comments').attr("checked", "checked");
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['group']->value['permissions']['delete_files']) {?>
        $('#perm_delete_files').attr("checked", "checked");
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['group']->value['permissions']['change_settings']) {?>
        $('#perm_change_settings').attr("checked", "checked");
    <?php }?>
    
    <?php if ($_smarty_tpl->tpl_vars['group']->value['permissions']['manage_contributors']) {?>
        $('#perm_manage_contributors').attr("checked", "checked");
    <?php }
echo '</script'; ?>
><?php }
}
