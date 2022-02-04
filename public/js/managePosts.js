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
    
    if (d.getMinutes() < 10) {
        minutes = '0' + d.getMinutes();
    }
    else {
        minutes = d.getMinutes()
    }

    var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
    return d.getHours() + ':' + minutes + ', ' + d.getDate() + suffix + ' ' + months[d.getMonth()] + ' ' + d.getFullYear();
}

var refreshData = function(blogid, pagenum) {
    pagenum = parseInt(pagenum);
    var numtoshow = $("#numtoshow").val();
    var sortby = $("#sortby").val();
    var filterdrafts = $("#filterdrafts").is(':checked');
    var filterscheduled = $("#filterscheduled").is(':checked');

    $.get("/api/posts",
        {
            blogID:         blogid,
            start:          pagenum,
            limit:          numtoshow,
            sort:           sortby,
            showdrafts:     filterdrafts,
            showscheduled:  filterscheduled

        }, function(data) {
            var start = pagenum * numtoshow - (numtoshow - 1);
            var end   = (pagenum * numtoshow) > data.postcount ? data.postcount : pagenum * numtoshow;
            var numpages = Math.ceil(data.postcount / numtoshow);
            var output = '';

            output += '<div class="ui secondary clearing segment">';
            output += '<a href="/cms/settings/posts/' + data.blog.id + '" class="ui right floated icon labeled button"><i class="cog icon"></i>Post settings</a>';  
            output += '<a href="/cms/posts/create/' + data.blog.id + '" class="ui right floated icon labeled teal button"><i class="plus icon"></i>New post</a>';  
            output += "<p class='ui right aligned'>Showing <strong>" + start + "</strong> - <strong>" + end + "</strong> of <strong>" + data.postcount + "</strong></p></div>";

                            
            output += "<table class='ui table'><thead>";
            output += "<tr><th></th><th>Title</th><th></th><th>Tag(s)</th><th>Author</th>";

            output += "<th>Visitors <a href='#' class='helptext' onclick='javascript:alert(\"This is the count of \'unique visitors\' for each post, not the number of times it has been viewed. So it will count 1 view even if someone refreshes the page multiple times\");'>[?]</a></th>";

            output += "<th>Views <a href='#' class='helptext' onclick='javascript:alert(\"This is the number of times each blog post has been loaded, if someone was to refresh the page 1000 times then it will show 1000 views, so this statistic may be unreliable\");'>[?]</a></th>";

            output += "<th>Type</th><th>Word count</th></tr></thead>";
        
            for(var i=0; i<numtoshow; i++) {
                                    
                var post = data.posts[i];
                
                if(!post) break;
                
                var tagoutput = "";
                
                if (post.tags.length > 0) {
                    var tags = post.tags.split(","); // todo: split out

                    for(var k=0; k<tags.length; k++) {
                        tag = tags[k].trim();
                        tag = tag.replace("+", " ");
                        tagoutput += `<div class="ui horizontal label"><a href="/blogs/${data.blog.id}/tags/${tag}">${tag}</a></div>`;
                    }
                }
                else {
                    tagoutput = "<i>None</i>";
                }
                
                output += "<tr><td><div class='ui fitted checkbox select_post'><input type='checkbox' data-post='" + post.id + "'><label></label></div></td><td>";
                // @todo apply custom domain compatible link
                output += ` <a href='/blogs/${data.blog.id}/posts/${post.link}'>${post.title}</a>`;
                
                if (new Date(post.timestamp) > new Date()) {
                    // Scheduled
                    output += " <i>Scheduled</i>";
                }
                
                if (post.draft == 1) {
                    // Draft
                    output += " <i>Draft</i>";
                }
                
                // todo: add scheduled and draft flags
                output += " <br><span class='date'>" + formatDate(post.timestamp) + "</span>";
                                    
                output += "</td><td width='100'>";
                output += " <div class='option-dropdown' style='width:100px;'>";
                output += "   <div class='default-option'>- Actions -</div>";
                output += "   <div class='hidden-options'>";
                output += "     <a href='/cms/posts/edit/" + post.id + "'>Edit</a>";
                output += "     <a class='clone_post_link' data-postid='" + post.id + "'>Clone</a>";
                output += "     <a class='delete_post_link' data-postid='" + post.id + "'>Delete</a>";
                output += "   </div>";
                output += " </div>";
                
                output += "</td><td>" + tagoutput;

                output += "</td><td>";
                output += " <a href='/account/user/" + post.author_id + "' class='user-link'>";
                output += "   <span data-userid='" + post.author_id + "'>" + post.username + "</span></a>";
                
                output += "</td><td>";
                output += " <div class='ui circular label'>" + post.uniqueviews + "</div>";

                output += "</td><td>";
                output += " <div class='ui circular label'>" + post.hits + "</div>";

                output += "</td><td>";
                output += " <div class='ui label'>" + post.type + "</div>";
                
                output += "</td><td>" + post.wordcount;
                output += " </td></tr>";
            }
        
            output += '</table>';
        
            output += '<div class="ui pagination menu">';

            // Don't show back link if current page is first page.
            if (pagenum == 1) {
                output += '<a class="disabled item"><i class="icon angle left"></i></a>';
            }
            else {
                output += `<a href="#" class="item" onclick="refreshData(${blogid}, ${pagenum-1}); return false;"><i class="icon angle left"></i></a>`;
            }

            var pstart = 1;
            var pend = numpages;

            if (numpages > 10) {
                pstart = pagenum - 5;
                if (pstart < 1) pstart = 1;

                pend = pstart + 10;
                if (pend > numpages) pend = numpages;
            }

            // loop through each page and give link to it.
            for (var j=pstart; j<=pend; j++) {
                if (pagenum == j) output += '<a class="active item">' + j + '</a>';
                else output += `<a href="#" class="item" onclick="refreshData(${blogid}, ${j}); return false;">` + j + '</a>';
            }
            // If last page don't give next link.
            if (pagenum < numpages) {
                output += `<a href="#" class="item" onclick="refreshData(${blogid}, ${pagenum+1}); return false;"><i class="icon angle right"></i></a>`;
            }
            else {
                output += '<a class="disabled item"><i class="icon angle right"></i></a>';
            }
        
            output += '</div>';

            $("#posts_display").html(output);
            
            $(".user-link").mouseenter(function() {
                showUserProfile($(this), "/", "/");
            });
            $(".user-link").mouseleave(function() {
                hideUserProfile($(this));
            });

            $(".select_post input[type=checkbox]").change(function () {
                if ($(".select_post input[type=checkbox]:checked").length > 0) $("#multi_post_options").show();
                else $("#multi_post_options").hide();
            });

            $(".delete_post_link").click(function(event) {
                event.preventDefault();
                $("#delete_post_button").data("postid", $(this).data('postid'));
                $("#delete_post_modal").modal('setting', 'closable', false).modal('show');
            });

            $(".clone_post_link").click(function(event) {
                event.preventDefault();
                $("#clone_post_button").data("postid", $(this).data('postid'));
                $("#clone_post_modal").modal('show');
            });
        }
    );
};