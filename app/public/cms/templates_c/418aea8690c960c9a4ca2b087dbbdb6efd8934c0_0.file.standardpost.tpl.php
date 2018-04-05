<?php
/* Smarty version 3.1.31, created on 2018-04-05 23:09:08
  from "C:\xampp_5.6.24\htdocs\rbwebdesigns\projects\blog_cms\app\view\smarty\posts\standardpost.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5ac69e8463f003_88723902',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '418aea8690c960c9a4ca2b087dbbdb6efd8934c0' => 
    array (
      0 => 'C:\\xampp_5.6.24\\htdocs\\rbwebdesigns\\projects\\blog_cms\\app\\view\\smarty\\posts\\standardpost.tpl',
      1 => 1522958903,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ac69e8463f003_88723902 (Smarty_Internal_Template $_smarty_tpl) {
if (isset($_smarty_tpl->tpl_vars['post']->value)) {?>

    
    <?php $_smarty_tpl->_assignInScope('formAction', "/cms/posts/edit/".((string)$_smarty_tpl->tpl_vars['post']->value['id']));
?>
    <?php $_smarty_tpl->_assignInScope('fieldTitle', $_smarty_tpl->tpl_vars['post']->value['title']);
?>
    <?php $_smarty_tpl->_assignInScope('fieldContent', $_smarty_tpl->tpl_vars['post']->value['content']);
?>
    <?php $_smarty_tpl->_assignInScope('fieldTags', str_replace("+"," ",$_smarty_tpl->tpl_vars['post']->value['tags']));
?>
    <?php $_smarty_tpl->_assignInScope('submitLabel', 'Update');
?>
    <?php $_smarty_tpl->_assignInScope('mode', 'edit');
?>
    <?php $_smarty_tpl->_assignInScope('postdate', date('m/d/Y g:ia',strtotime($_smarty_tpl->tpl_vars['post']->value['timestamp'])));
?>

<?php } else { ?>
    
    <?php $_smarty_tpl->_assignInScope('formAction', "/cms/posts/create/".((string)$_smarty_tpl->tpl_vars['blog']->value['id'])."/standard");
?>
    <?php $_smarty_tpl->_assignInScope('fieldTitle', '');
?>
    <?php $_smarty_tpl->_assignInScope('fieldContent', '');
?>
    <?php $_smarty_tpl->_assignInScope('fieldTags', '');
?>
    <?php $_smarty_tpl->_assignInScope('submitLabel', 'Create');
?>
    <?php $_smarty_tpl->_assignInScope('mode', 'create');
?>
    <?php $_smarty_tpl->_assignInScope('postdate', date('m/d/Y g:ia'));
}?>

<?php echo '<script'; ?>
 type="text/javascript">
// this is now out of date as we have the rtf editor
// handy little script however...
function displayHTML() {
    var inf = document.upload_data.fld_postcontent.value;
    win = window.open(", ", 'popup', 'toolbar = no, status = no, width = 600, height = 400');
    win.document.write("" + inf + "");
}

// This is also not used... but was an interesting concept
function openPreview() {
    var previewWin = window.open("/blogs/<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
/posts/post.link","_blank","height=600,width=800");
    $(previewWin).load(function() {
        alert(typeof previewWin.document);
        previewWin.document.getElementById('comments').innerHTML = '';
    });
}
<?php echo '</script'; ?>
>

<div class="ui grid">
    
    <div class="one column row">
        <div class="column">
            <?php echo viewCrumbtrail(array("/cms/blog/overview/".((string)$_smarty_tpl->tpl_vars['blog']->value['id']),((string)$_smarty_tpl->tpl_vars['blog']->value['name'])),'New Post');?>

        </div>
    </div>
    
    <div class="one column row">
        <div class="column">
            <?php echo viewPageHeader(((string)$_smarty_tpl->tpl_vars['submitLabel']->value)." Blog Post",'add_doc.png',((string)$_smarty_tpl->tpl_vars['blog']->value['name']));?>


            <?php if ($_smarty_tpl->tpl_vars['mode']->value == 'edit' && array_key_exists('autosave',$_smarty_tpl->tpl_vars['post']->value)) {?>
            <?php echo '<script'; ?>
>
                function replaceContent() {
                    $("#fld_posttitle").val($("#fld_autosave_title").val());
                    $("#fld_postcontent").val($("#fld_autosave_content").val());
                    $("#fld_tags").val($("#fld_autosave_tags").val());
                    $("#autosave_data").hide();
                    $("#autosave_exists_message").hide();
                 }
            <?php echo '</script'; ?>
>

            <div id="autosave_exists_message" class="ui yellow segment clearing">
                <p>You have an autosaved draft for this post, do you want to continue with this edit?
                <a href="#" onclick="$('#autosave_data').toggle(); return false;" class="ui basic right floated teal button">Show Content</a>
                <a href="#" onclick="$('#autosave_data').hide(); $('#autosave_exists_message').hide(); return false;" class="ui right floated teal button">No</a>
                <a href="#" onclick="replaceContent(); return false;" class="ui right floated teal button">Yes</a></p>
            </div>
            
            <div id="autosave_data" class="ui segment" style="display:none;">
                <div class="ui form">
                    <h2 class="ui heading">Autosaved Post</h2>
                    <div class="field">
                        <label for="fld_autosave_title">Title</label>
                        <input disabled class="" type="text" id="fld_autosave_title" name="fld_autosave_title" value="<?php echo $_smarty_tpl->tpl_vars['post']->value['autosave']['title'];?>
" />
                    </div>
                    <div class="field">
                        <label for="fld_autosave_content">Content</label>
                        <textarea disabled id="fld_autosave_content" name="fld_autosave_content"><?php echo $_smarty_tpl->tpl_vars['post']->value['autosave']['content'];?>
</textarea>
                    </div>
                    <div class="field">
                        <label for="fld_autosave_tags">Tags</label>
                        <input disabled type="text" id="fld_autosave_tags" name="fld_autosave_tags" value="<?php echo $_smarty_tpl->tpl_vars['post']->value['autosave']['tags'];?>
" />
                    </div>
                </div>
            </div>
            <?php }?>
        </div>
    </div>

    <form action="<?php echo $_smarty_tpl->tpl_vars['formAction']->value;?>
" method="post" name="frm_createpost" id="frm_createpost" class="two column row ui form">
        
        <div class="ten wide column">
            
            <div class="field">
                <label for="fld_posttitle">Title</label>
                <input type="text" name="fld_posttitle" id="fld_posttitle" size="50" autocomplete="off" value="<?php echo $_smarty_tpl->tpl_vars['fieldTitle']->value;?>
" />
            </div>
            
            <div class="field">
                <label for="fld_postcontent">Content</label>
                <button type="button" onclick="rbrtf_showWindow('/ajax/add_image?blogid=<?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
')" title="Insert Image"><img src="/resources/icons/document_image_add_32.png" style="width:15px; height:15px;" /></button>
                <p style="font-size:80%;">Note - <a href="https://daringfireball.net/projects/markdown/syntax" target="_blank">Markdown</a> is supported!</p>
                <textarea name="fld_postcontent" id="fld_postcontent" style="height:30vh;"><?php echo $_smarty_tpl->tpl_vars['fieldContent']->value;?>
</textarea>
            </div>
            
            <div class="field">
                <label for="fld_tags">Tags</label>
                <input type="text" name="fld_tags" id="fld_tags" placeholder="Enter as a Comma Seperated List" autocomplete="off" value="<?php echo $_smarty_tpl->tpl_vars['fieldTags']->value;?>
" />
            </div>
            
            <div id="autosave_status" class="ui positive message" style="display:none;"></div>

            <?php if ($_smarty_tpl->tpl_vars['mode']->value == 'edit') {?>
              <input type="hidden" id="fld_postid" name="fld_postid" value="<?php echo $_smarty_tpl->tpl_vars['post']->value['id'];?>
">
            <?php } else { ?>
              <input type="hidden" id="fld_postid" name="fld_postid" value="0">
            <?php }?>
            
            <input type="hidden" name="fld_posttype" id="fld_posttype" value="standard">
            
            <input type="button" value="Cancel" name="goback" onclick="if(confirm('You will lose any changes made')) { window.location = '/posts/cancelsave/' + $('#fld_postid').val(); window.content_changed = false; }" class="ui button right floated">
            <input type="submit" name="fld_submitpost" value="<?php echo $_smarty_tpl->tpl_vars['submitLabel']->value;?>
" class="ui button teal right floated">
        </div>


        <div class="six wide column">
            
            <div class="field">
                <label for="fld_postdate">Schedule Post
                    <a href="/" onclick="alert('Set the date and time for this post to show on your blog.'); return false;">[?]</a></label>
                <div class="ui calendar" id="postdate">
                    <div class="ui input left icon">
                        <i class="calendar icon"></i>
                        <input type="text" name="fld_postdate" placeholder="Date/Time" value="<?php echo $_smarty_tpl->tpl_vars['postdate']->value;?>
">
                    </div>
                </div>
                <?php echo '<script'; ?>
>$('#postdate').calendar();<?php echo '</script'; ?>
>
            </div>
 
            <div class="field">
                <label for="fld_allowcomment">Allow Comments <a href="/" onclick="alert('This option will allow logged in users to post comments on your blog posts. You can control whether these are shown automatically in the blog settings.'); return false;">[?]</a></label>
                <select name="fld_allowcomment" id="fld_allowcomment">
                    <option value="1">Yes</option>
                    <option value="0">No</option>
                </select>
            </div>

            <div class="field">
                <label for="fld_draft">Action to take <a href="/" onclick="alert('Post to Blog: This post will be live on your blog for anyone that can see you blog to read. Blog security settings can be changed in the settings section.\n\nSave as Draft: The post will be saved for further editing later and will not be visible to your readers.'); return false;">[?]</a></label>
                <select name="fld_draft" id="fld_draft">
                    <option value="0">Post to Blog</option>
                    <option value="1">Save as Draft</option>
                </select>
            </div>

        </div>
    </form>


<?php echo '<script'; ?>
 type="text/javascript">
var content_changed = false;
window.postTitleIsValid = false;
    
$(document).ready(function () {

    $(window).on('beforeunload', function()
    {
        if(content_changed && !confirm('Are you sure you want to leave this page - you may lose changes?'))
        {
            return false;
        }
    });
    
    // Auto save
    var runsave = function()
    {
        if(content_changed)
        {
            jQuery.post("/ajax/autosave",
            {
                "fld_postid": $("#fld_postid").val(),
                "fld_content": $("#fld_postcontent").val(),
                "fld_title": $("#fld_posttitle").val(),
                "fld_type": $("#fld_posttype").val(),
                "fld_allowcomments": $("#fld_allowcomment").val(),
                "fld_tags": $("#fld_tags").val(),
                "fld_blogid": <?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
,
                "csrf_token": CSRFTOKEN

            }, function(data)
            {
                if(typeof data.newpostid != "null" && typeof data.newpostid != "undefined")
                {
                    $("#fld_postid").val(data.newpostid);
                }
                $("#autosave_status").html(data.message);
                $("#autosave_status").show();

                content_changed = false;

            }, "json");
        }
    }

    // Run on key down of content
    $("#fld_postcontent").on("keyup", function() { content_changed = true; });
    $("#fld_posttitle").on("keyup", function() { window.postTitleIsValid = false; content_changed = true; });
    $("#fld_tags").on("keyup", function() { content_changed = true; });

    // Saves every 10 seconds if something has changed
    var save_interval = setInterval(runsave, 5000);

    // Function to submit form
    var submitForm = function () {
        document.frm_createpost.submit();
    };

    // Handle form submission
    $("#frm_createpost").submit(function() {

        $(window).unbind('beforeunload');

        // Check that the post title is unique
        var post_title = document.getElementById('fld_posttitle').value;
        var blog_id = <?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
;

        if (post_title.length == 0) {
            alert("Please enter a title");
            return false;
        }

        <?php if (isset($_smarty_tpl->tpl_vars['post']->value)) {?>
            var url = "/ajax/checkDuplicateTitle?blog_id=" + blog_id + "&post_title=" + post_title + "&post_id=<?php echo $_smarty_tpl->tpl_vars['post']->value['id'];?>
";
        <?php } else { ?>
            var url = "/ajax/checkDuplicateTitle?blog_id=" + blog_id + "&post_title=" + post_title;
        <?php }?>

        $.ajax({ url: url, async: false }).done(function(data) {
            if(data.trim() !== "false") {
                alert("Validation Failed - Title needs to be unique for this blog!");
            }
            else {
                console.log('a');
                window.postTitleIsValid = true;
            }
        });

        return window.postTitleIsValid;
    });

    <?php if ($_smarty_tpl->tpl_vars['mode']->value == 'edit') {?>
        // Apply Defaults
        $("#fld_draft").val(<?php echo $_smarty_tpl->tpl_vars['post']->value['draft'];?>
);
        $("#fld_allowcomment").val(<?php echo $_smarty_tpl->tpl_vars['post']->value['allowcomments'];?>
);
    <?php }?>

});
<?php echo '</script'; ?>
>
</div><?php }
}
