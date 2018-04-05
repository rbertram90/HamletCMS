<?php
/* Smarty version 3.1.31, created on 2018-04-05 23:09:38
  from "C:\xampp_5.6.24\htdocs\rbwebdesigns\projects\blog_cms\app\view\smarty\account\editdetails.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5ac69ea2bea5d9_28736720',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e29670733787617ca8aef8b56ffc3ee3353d278e' => 
    array (
      0 => 'C:\\xampp_5.6.24\\htdocs\\rbwebdesigns\\projects\\blog_cms\\app\\view\\smarty\\account\\editdetails.tpl',
      1 => 1522958722,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ac69ea2bea5d9_28736720 (Smarty_Internal_Template $_smarty_tpl) {
if (!is_callable('smarty_modifier_date_format')) require_once 'C:\\xampp_5.6.24\\htdocs\\rbwebdesigns\\projects\\blog_cms\\app\\vendor\\smarty\\smarty\\libs\\plugins\\modifier.date_format.php';
?>
<div class="ui grid">
    <div class="one column row">
        <div class="column">
            <?php echo viewCrumbtrail(array("/cms/account/user",'Account'),'Edit');?>

        </div>
    </div>
    <div class="one column row">
        <div class="column">
            <?php echo viewPageHeader('Your Information','id.png');?>

        </div>
    </div>
</div>

<div class="ui three item menu">
  <a href="/cms/account/settings" class="active item">Settings</a>
  <a href="/cms/account/password" class="item">Change Password</a>
  <a href="/cms/account/avatar" class="item">Upload Avatar</a>
</div>

<p class="ui message info">Information provided is not passed on to third parties</p>

<form action="/cms/account/settings" method="POST" class="ui form">

    <div class="two fields">
        <div class="field">
            <label for="fld_firstname">First Name</label>
            <input type="text" name="fld_firstname" value="<?php echo $_smarty_tpl->tpl_vars['user']->value['name'];?>
" onkeyup="validate(this,{fieldlength:2})">
        </div>
        
        <div class="field">
            <label for="fld_surname">Surname</label>
            <input type="text" name="fld_surname" value="<?php echo $_smarty_tpl->tpl_vars['user']->value['surname'];?>
" onkeyup="validate(this,{fieldlength:2})">
        </div>
    </div>
    <div class="two fields">
        <div class="field">
            <label for="fld_username">Username</label>
            <input type="text" name="fld_username" value="<?php echo $_smarty_tpl->tpl_vars['user']->value['username'];?>
">
        </div>

        <div class="field">
            <label for="fld_email">Email</label>
            <input type="text" name="fld_email" onkeyup="validate(this,{email:true}})" value="<?php echo $_smarty_tpl->tpl_vars['user']->value['email'];?>
" />
        </div>
    </div>
    
    <div class="field">
        <label for="fld_description">Description</label>
        <textarea name="fld_description"><?php echo $_smarty_tpl->tpl_vars['user']->value['description'];?>
</textarea>
    </div>
    
    <?php $_smarty_tpl->_assignInScope('dob', getdate(strtotime($_smarty_tpl->tpl_vars['user']->value['dob'])));
?>

    <div class="inline fields">
        <label for='fld_dob_day'>Date of Birth</label>
        <div class="field">        
            <select name="fld_dob_day" class="ui dropdown">
            <?php
$_smarty_tpl->tpl_vars['i'] = new Smarty_Variable(null, $_smarty_tpl->isRenderingCache);$_smarty_tpl->tpl_vars['i']->step = 1;$_smarty_tpl->tpl_vars['i']->total = (int) ceil(($_smarty_tpl->tpl_vars['i']->step > 0 ? 31+1 - (1) : 1-(31)+1)/abs($_smarty_tpl->tpl_vars['i']->step));
if ($_smarty_tpl->tpl_vars['i']->total > 0) {
for ($_smarty_tpl->tpl_vars['i']->value = 1, $_smarty_tpl->tpl_vars['i']->iteration = 1;$_smarty_tpl->tpl_vars['i']->iteration <= $_smarty_tpl->tpl_vars['i']->total;$_smarty_tpl->tpl_vars['i']->value += $_smarty_tpl->tpl_vars['i']->step, $_smarty_tpl->tpl_vars['i']->iteration++) {
$_smarty_tpl->tpl_vars['i']->first = $_smarty_tpl->tpl_vars['i']->iteration == 1;$_smarty_tpl->tpl_vars['i']->last = $_smarty_tpl->tpl_vars['i']->iteration == $_smarty_tpl->tpl_vars['i']->total;?>
                <?php if ($_smarty_tpl->tpl_vars['dob']->value['mday'] == $_smarty_tpl->tpl_vars['i']->value) {?>
                    <option value='<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
' selected><?php echo $_smarty_tpl->tpl_vars['i']->value;?>
</option>
                <?php } else { ?>
                    <option value='<?php echo $_smarty_tpl->tpl_vars['i']->value;?>
'><?php echo $_smarty_tpl->tpl_vars['i']->value;?>
</option>
                <?php }?>
            <?php }
}
?>

            </select> /
        </div>
        <div class="field">
            <select name="fld_dob_month" class="ui dropdown">
            <?php
$_smarty_tpl->tpl_vars['j'] = new Smarty_Variable(null, $_smarty_tpl->isRenderingCache);$_smarty_tpl->tpl_vars['j']->step = 1;$_smarty_tpl->tpl_vars['j']->total = (int) ceil(($_smarty_tpl->tpl_vars['j']->step > 0 ? 12+1 - (1) : 1-(12)+1)/abs($_smarty_tpl->tpl_vars['j']->step));
if ($_smarty_tpl->tpl_vars['j']->total > 0) {
for ($_smarty_tpl->tpl_vars['j']->value = 1, $_smarty_tpl->tpl_vars['j']->iteration = 1;$_smarty_tpl->tpl_vars['j']->iteration <= $_smarty_tpl->tpl_vars['j']->total;$_smarty_tpl->tpl_vars['j']->value += $_smarty_tpl->tpl_vars['j']->step, $_smarty_tpl->tpl_vars['j']->iteration++) {
$_smarty_tpl->tpl_vars['j']->first = $_smarty_tpl->tpl_vars['j']->iteration == 1;$_smarty_tpl->tpl_vars['j']->last = $_smarty_tpl->tpl_vars['j']->iteration == $_smarty_tpl->tpl_vars['j']->total;?>
                <?php if ($_smarty_tpl->tpl_vars['dob']->value['mon'] == $_smarty_tpl->tpl_vars['j']->value) {?>
                    <option value='<?php echo $_smarty_tpl->tpl_vars['j']->value;?>
' selected><?php echo smarty_modifier_date_format("01-".((string)$_smarty_tpl->tpl_vars['j']->value)."-1980","%B");?>
</option>
                <?php } else { ?>
                    <option value='<?php echo $_smarty_tpl->tpl_vars['j']->value;?>
'><?php echo smarty_modifier_date_format("01-".((string)$_smarty_tpl->tpl_vars['j']->value)."-1980","%B");?>
</option>
                <?php }?>
            <?php }
}
?>

            </select> /
        </div>
        <div class="field">
            <select name="fld_dob_year" class="ui dropdown">
            <?php
$_smarty_tpl->tpl_vars['k'] = new Smarty_Variable(null, $_smarty_tpl->isRenderingCache);$_smarty_tpl->tpl_vars['k']->step = -1;$_smarty_tpl->tpl_vars['k']->total = (int) ceil(($_smarty_tpl->tpl_vars['k']->step > 0 ? 1900+1 - (date("Y")) : date("Y")-(1900)+1)/abs($_smarty_tpl->tpl_vars['k']->step));
if ($_smarty_tpl->tpl_vars['k']->total > 0) {
for ($_smarty_tpl->tpl_vars['k']->value = date("Y"), $_smarty_tpl->tpl_vars['k']->iteration = 1;$_smarty_tpl->tpl_vars['k']->iteration <= $_smarty_tpl->tpl_vars['k']->total;$_smarty_tpl->tpl_vars['k']->value += $_smarty_tpl->tpl_vars['k']->step, $_smarty_tpl->tpl_vars['k']->iteration++) {
$_smarty_tpl->tpl_vars['k']->first = $_smarty_tpl->tpl_vars['k']->iteration == 1;$_smarty_tpl->tpl_vars['k']->last = $_smarty_tpl->tpl_vars['k']->iteration == $_smarty_tpl->tpl_vars['k']->total;?>
                <?php if (intval($_smarty_tpl->tpl_vars['dob']->value['year']) == $_smarty_tpl->tpl_vars['k']->value) {?>
                    <option value='<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
' selected><?php echo $_smarty_tpl->tpl_vars['k']->value;?>
</option>
                <?php } else { ?>
                    <option value='<?php echo $_smarty_tpl->tpl_vars['k']->value;?>
'><?php echo $_smarty_tpl->tpl_vars['k']->value;?>
</option>
                <?php }?>
            <?php }
}
?>

            </select>
        </div>
    </div>

    <div class="two fields">
        <div class="field">
            <label for="fld_gender">Gender</label>
            <select name="fld_gender" class="ui dropdown">
                <?php if ($_smarty_tpl->tpl_vars['user']->value['gender'] == 'Male') {?>
                    <option selected>Male</option>
                    <option>Female</option>
                <?php } else { ?>
                    <option>Male</option>
                    <option selected>Female</option>
                <?php }?>
            </select>
        </div>

        <div class="field">
            <label for="fld_location">Location</label>
            <input type="text" name="fld_location" value="<?php echo $_smarty_tpl->tpl_vars['user']->value['location'];?>
">
        </div>
    </div>

    <div style="text-align:right; width:100%;">
        <input type="submit" name="fld_submit_accchange" value="Update Account" class="ui teal button" />
    </div>

</form>

<?php echo '<script'; ?>
>
$('select.dropdown').dropdown();
<?php echo '</script'; ?>
><?php }
}
