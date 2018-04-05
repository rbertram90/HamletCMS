<?php
/* Smarty version 3.1.31, created on 2018-04-05 23:09:21
  from "C:\xampp_5.6.24\htdocs\rbwebdesigns\projects\blog_cms\app\view\smarty\contributors\creategroup.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5ac69e9181e422_73495166',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '2df6883ccecd6d1983f9b9c7251a3dc610f44233' => 
    array (
      0 => 'C:\\xampp_5.6.24\\htdocs\\rbwebdesigns\\projects\\blog_cms\\app\\view\\smarty\\contributors\\creategroup.tpl',
      1 => 1522958764,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ac69e9181e422_73495166 (Smarty_Internal_Template $_smarty_tpl) {
?>
<div class="ui grid">
    <div class="row">
        <div class="column">
            <?php echo viewCrumbtrail(array("/cms/blog/overview/".((string)$_smarty_tpl->tpl_vars['blog']->value['id']),((string)$_smarty_tpl->tpl_vars['blog']->value['name']),"/cms/contributors/manage/".((string)$_smarty_tpl->tpl_vars['blog']->value['id']),"Contributors"),'Add Group');?>

        </div>
    </div>
    <div class="row">
        <div class="column">
            <?php echo viewPageHeader("Add Group",'friends.png',((string)$_smarty_tpl->tpl_vars['blog']->value['name']));?>

        </div>
    </div>
    <div class="row">
        <div class="column">
            <form class="ui form" method="POST">
                <h2>Basic Information</h2>
                <div class="field">
                    <label for="fld_name">Group Name</label>
                    <input type="text" value="" name="fld_name" id="fld_name">
                </div>

                <div class="field">
                    <label for="fld_description">Description</label>
                    <input type="text" value="" name="fld_description" id="fld_description">
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
<?php echo '</script'; ?>
><?php }
}
