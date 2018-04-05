<?php
/* Smarty version 3.1.31, created on 2018-04-05 23:09:03
  from "C:\xampp_5.6.24\htdocs\rbwebdesigns\projects\blog_cms\app\view\smarty\posts\manage.tpl" */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.31',
  'unifunc' => 'content_5ac69e7f064321_49926810',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e9f7d4ee549f74aaf3f0fa0d667279385afa8906' => 
    array (
      0 => 'C:\\xampp_5.6.24\\htdocs\\rbwebdesigns\\projects\\blog_cms\\app\\view\\smarty\\posts\\manage.tpl',
      1 => 1522966141,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5ac69e7f064321_49926810 (Smarty_Internal_Template $_smarty_tpl) {
?>


<div class="ui grid">
    
    <div class="one column row">
        <div class="column">
            <?php echo viewCrumbtrail(array("/blog/overview/".((string)$_smarty_tpl->tpl_vars['blog']->value['id']),((string)$_smarty_tpl->tpl_vars['blog']->value['name'])),'Manage Posts');?>

        </div>
    </div>
    
    <div class="two column row">
        <div class="column">
            <?php echo viewPageHeader('Manage Posts','papers.png',((string)$_smarty_tpl->tpl_vars['blog']->value['name']));?>

        </div>
        <div class="column">
            <div class="ui form">
                <div class="ui horizontal segments margin" style="margin:0;">
                    <div class="ui segment">
                        <div class="field">
                            <label for="numtoshow">Show</label>
                            <select id="numtoshow" name="numtoshow" class="ui dropdown">
                                <option>5</option>
                                <option selected>10</option>
                                <option>15</option>
                                <option>20</option>
                            </select>
                        </div>
                    </div>
                    <div class="ui segment">
                        <div class="field">
                            <label for="sortby">Sort</label>
                            <select id="sortby" name="sortby" class="ui dropdown">
                                <option value="timestamp DESC">Date Posted (Newest First)</option>
                                <option value="timestamp ASC">Date Posted (Oldest First)</option>
                                <option value="title ASC">Title (A First)</option>
                                <option value="title DESC">Title (Z First)</option>
                                <option value="author_id ASC">Author ID (Low -> High)</option>
                                <option value="author_id DESC">Author ID (High -> Low)</option>
                                <option value="hits DESC">Views (Most First)</option>
                                <option value="hits ASC">Views (Least First)</option>
                                <option value="uniqueviews DESC">Visitors (Most First)</option>
                                <option value="uniqueviews ASC">Visitors (Least First)</option>
                                <option value="numcomments DESC">Comments (Most First)</option>
                                <option value="numcomments ASC">Comments (Least First)</option>
                            </select>
                        </div>
                    </div>
                    <div class="ui segment">
                    <div class="inline field">
                        <div class="ui checkbox">
                            <input type="checkbox" class="hidden" id="filterdrafts" name="filterdrafts" checked>
                            <label>Show Drafts</label>
                        </div>
                    </div>
                    <div class="inline field">
                        <div class="ui checkbox">
                            <input type="checkbox" class="hidden" id="filterscheduled" name="filterscheduled" checked />
                            <label>Show Scheduled</label>
                        </div>
                    </div>
                    </div>
                </div>
            </div>
            <?php echo '<script'; ?>
>$('.ui.checkbox').checkbox();<?php echo '</script'; ?>
>
        </div>
    </div>
    
    <div class="one column row">
        <div class="column">
            <div id="posts_display">Loading...</div>
        </div>
    </div>
    
</div>
    
<?php echo '<script'; ?>
>
    // E.g. 3:11, 22nd Aug 2015
    function formatDate(date) {
        var d = new Date(date);
        
        switch(d.getDate()) {
            case 1,21,31:
                var suffix = 'st';
                break;
            case 2,22:
                var suffix = 'nd';
                break;
            case 3,23:
                var suffix = 'rd';
                break;
            default:
                var suffix = 'th';
                break;
        }
        
        var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
        return d.getHours() + ':' + d.getMinutes() + ', ' + d.getDate() + suffix + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
    }
    
    var refreshData = function(pagenum) {
        var numtoshow = $("#numtoshow").val();
        var sortby = $("#sortby").val();
        var filterdrafts = $("#filterdrafts").is(':checked');
        var filterscheduled = $("#filterscheduled").is(':checked');

        $.get("/cms/api/posts",
            {
                blogID:         <?php echo $_smarty_tpl->tpl_vars['blog']->value['id'];?>
,
                start:          pagenum,
                limit:          numtoshow,
                sort:           sortby,
                showdrafts:     filterdrafts,
                showscheduled:  filterscheduled

            }, function(data) {
                // $("#posts_display").html(data);
                
                var start = pagenum * numtoshow - (numtoshow - 1);
                var end   = pagenum * numtoshow;
                var numpages = Math.ceil(data.postcount / numtoshow);
                output = "<p>Showing " + start + " - " + end + " of " + data.postcount + "</p>";
                
                output += "<table class='ui table'><thead>";
                output += "<tr><th>Title</th><th>Tag(s)</th><th>Author</th>";
    
                output += "<th>Visitors <a href='#' class='helptext' onclick='javascript:alert(\"This is the count of \'unique visitors\' for each post, not the number of times it has been viewed. So it will count 1 view even if someone refreshes the page multiple times\");'>[?]</a></th>";
    
                output += "<th>Views <a href='#' class='helptext' onclick='javascript:alert(\"This is the number of times each blog post has been loaded, if someone was to refresh the page 1000 times then it will show 1000 views, so this statistic may be unreliable\");'>[?]</a></th>";
    
                output += "<th>Comments</th><th>Type</th><th>Word Count</th><th></th></tr></thead>";
            
                for(var i=0; i<numtoshow; i++) {
                                        
                    var post = data.posts[i];
                    
                    if(!post) break;
                    
                    var tagoutput = "";
                    
                    if(post.tags.length > 0) {
                        var tags = post.tags.split(","); // todo: split out

                        for(var k=0; k<tags.length; k++) {
                            tag = tags[k].trim();
                            tag = tag.replace("+", " ");
                            tagoutput += "<div class='ui horizontal label'><a href='/blogs/" + data.blog.id + "/tags/" + tag + "'>" + tag + "</a></div>";
                        }
                    }
                    else {
                        tagoutput = "<i>None</i>";
                    }
                    
                    output += "<tr><td>";
                    output += " <a href='/blogs/" + data.blog.id + "/posts/" + post.link + "'>" + post.title + "</a>"
                    
                    if(new Date(post.timestamp) > new Date()) {
                        // Scheduled
                        output += " <i>Scheduled</i>";
                    }
                    
                    if(post.draft == 1) {
                        // Draft
                        output += " <i>Draft</i>";
                    }
                    
                    // todo: add scheduled and draft flags
                    output += " <br><span class='date'>" + formatDate(post.timestamp) + "</span>";
                    
                    output += "</td><td>" + tagoutput;

                    output += "</td><td>";
                    output += " <a href='/account/user/" + post.author_id + "' class='user-link'>";
                    output += "   <span data-userid='" + post.author_id + "'>" + post.username + "</span></a>";
                    
                    output += "</td><td>";
                    output += " <div class='ui circular label'>" + post.uniqueviews + "</div>";

                    output += "</td><td>";
                    output += " <div class='ui circular label'>" + post.hits + "</div>";

                    output += "</td><td>";
                    output += " <div class='ui circular label'>" + post.numcomments + "</div>";

                    output += "</td><td>";
                    
                    switch(post.type.toLowerCase()) {
                        case 'video':
                            typecolour = 'purple';
                            break;
                        case 'gallery':
                            typecolour = 'orange';
                            break;
                        default:
                            typecolour = '';
                            break;
                    }
                    
                    output += " <div class='ui label " + typecolour + "'>" + post.type + "</div>";
                    
                    output += "</td><td>" + post.wordcount;

                    output += "</td><td width='100'>";
                    output += " <div class='option-dropdown' style='width:100px;'>";
                    output += "   <div class='default-option'>- Actions -</div>";
                    output += "   <div class='hidden-options'>";
                    output += "     <a href='/cms/posts/edit/" + post.id + "'>Edit</a>";
                    output += "     <a href='/cms/posts/delete/" + post.id + "' onclick='return confirm(\"Are you sure you want to delete this post?\");'>Delete</a>";
                    output += " </div></div>";

                    output += " </td></tr>";
                    
                }
            
                output += '</table>';
            
                output += '<div class="ui pagination menu">';

                // Don't show back link if current page is first page.
                if (pagenum == 1) {
                    output += '<a class="disabled item">&lt;</a>';
                }
                else {
                    output += '<a href="#" class="item" onclick="refreshData(\'' + (pagenum-1) + '\'); return false;">&lt;</a>';
                }
                // loop through each page and give link to it.
                for (var j=1; j<=numpages; j++) {
                    if (pagenum == j) output += '<a class="active item">' + j + '</a>';
                    else output += '<a href="#" class="item" onclick="refreshData(\'' + j + '\'); return false;">' + j + '</a>';
                }
                // If last page don't give next link.
                if (pagenum < numpages) {
                    output += '<a href="#" class="item" onclick="refreshData(\'' + (pagenum+1) + '\'); return false;">&gt;</a>';
                }
                else {
                    output += '<a class="disabled item">&gt;</a>';
                }
            
                output += '</div>';
            
                output += '<a href="/posts/create/' + data.blog.id + '" class="ui button teal right floated">New Post</a>';
                
                output += '<?php echo '<script'; ?>
>';
                output += '  $(".user-link").mouseenter(function() { showUserProfile($(this), "/", "/") });';
                output += '  $(".user-link").mouseleave(function() { hideUserProfile($(this)) });';
                output += '<\/script>';
            
                $("#posts_display").html(output);
            }
        );
    };
    
    // change number that is shown - return to first page
    $("#numtoshow").change(function()       { refreshData(1); });
    $("#sortby").change(function()          { refreshData(1); });
    $("#filterdrafts").change(function()    { refreshData(1); });
    $("#filterscheduled").change(function() { refreshData(1); });

    // Init
    refreshData(1);

<?php echo '</script'; ?>
><?php }
}
