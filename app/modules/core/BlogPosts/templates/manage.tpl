{* Manage Posts *}

<div class="ui grid">
    <div class="one column row">
        <div class="column">
            <div class="ui form">
                <div class="ui horizontal segments margin" style="margin:0;">
                    <div class="ui segment">
                        <div class="field">
                            <label for="numtoshow">Show</label>
                            <select id="numtoshow" name="numtoshow" class="ui fluid dropdown">
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
                            <select id="sortby" name="sortby" class="ui fluid dropdown">
                                <option value="timestamp DESC">Date posted (newest first)</option>
                                <option value="timestamp ASC">Date posted (oldest first)</option>
                                <option value="title ASC">Title (A - Z)</option>
                                <option value="title DESC">Title (Z - A)</option>
                                <option value="author_id ASC">Author ID (low > high)</option>
                                <option value="author_id DESC">Author ID (high > low)</option>
                                <option value="hits DESC">Views (most first)</option>
                                <option value="hits ASC">Views (least first)</option>
                                <option value="uniqueviews DESC">Visitors (most first)</option>
                                <option value="uniqueviews ASC">Visitors (least first)</option>
                            </select>
                        </div>
                    </div>
                    <div class="ui segment">
                        <div class="inline field">
                            <div class="ui checkbox">
                                <input type="checkbox" class="hidden" id="filterdrafts" name="filterdrafts" checked>
                                <label>Show drafts</label>
                            </div>
                        </div>
                        <div class="inline field">
                            <div class="ui checkbox">
                                <input type="checkbox" class="hidden" id="filterscheduled" name="filterscheduled" checked />
                                <label>Show scheduled</label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <script>$('.ui.checkbox').checkbox();</script>
            <script>$('.ui.dropdown').dropdown();</script>
        </div>
    </div>

    <div class="one column row" id="multi_post_options" style="display:none;">
        <div class="column">
            <div class="ui yellow primary clearing segment">
                With selected:
                <select class="ui dropdown" id="bulk_action_name">
                    <option value="unpublish">Set as draft</option>
                    <option value="publish">Post to blog</option>
                    <option value="clone">Clone</option>
                    <option value="delete">Delete</option>
                </select>
                <button class="ui teal button" id="submit_bulk_action" data-no-spinner="true">Action</button>
            </div>
        </div>
    </div>
    
    <div class="one column row">
        <div class="column">
            <div id="manage_posts_messages"></div>
            <div id="posts_display"><img src="/images/ajax-loader.gif" alt="Loading..."></div>
        </div>
    </div>
    
</div>
    
<script>    
    // change number that is shown - return to first page
    $("#numtoshow").change(function()       { refreshData({$blog->id}, 1); });
    $("#sortby").change(function()          { refreshData({$blog->id}, 1); });
    $("#filterdrafts").change(function()    { refreshData({$blog->id}, 1); });
    $("#filterscheduled").change(function() { refreshData({$blog->id}, 1); });

    $(".ui.dropdown").dropdown();

    // Init
    refreshData({$blog->id}, 1);
</script>

<div class="ui basic modal" id="delete_post_modal">
  <div class="ui icon huge header">
    <i class="trash alternate outline icon"></i>
    Delete post
  </div>
  <div class="content" style="text-align:center;">
    <p>Are you sure you want to delete this post?</p>
  </div>
  <div class="actions" style="text-align:center;">
    <a class="big ui green ok inverted button" id="delete_post_button">
      <i class="checkmark icon"></i>
      Delete
    </a>
    <div class="big ui red basic cancel inverted button">
      <i class="remove icon"></i>
      Nevermind
    </div>
  </div>
</div>

<div class="ui basic modal" id="clone_post_modal">
  <div class="ui icon huge header">
    <i class="copy outline icon"></i>
    Clone post
  </div>
  <div class="content" style="text-align:center;">
    <p>Are you sure you want to clone this post?</p>
  </div>
  <div class="actions" style="text-align:center;">
    <a class="big ui green ok inverted button" id="clone_post_button">
      <i class="checkmark icon"></i>
      Clone
    </a>
    <div class="big ui red basic cancel inverted button">
      <i class="remove icon"></i>
      Nevermind
    </div>
  </div>
</div>

<div class="ui basic modal" id="bulk_post_modal">
  <div class="ui icon huge header">
    <i class="question circle outline icon"></i>
    Bulk <span class="action-name">delete</span> posts
  </div>
  <div class="content" style="text-align:center;">
    <p>Are you sure you want to <span class="action-name">delete</span> selected posts?</p>
  </div>
  <div class="actions" style="text-align:center;">
    <a class="big ui green ok inverted button" id="bulk_post_button" data-no-spinner="true">
      <i class="checkmark icon"></i>
      Yes, <span class="action-name">delete</span> <span class="action-count">2</span> post(s)
    </a>
    <div class="big ui red basic cancel inverted button">
      <i class="remove icon"></i>
      Nevermind
    </div>
  </div>
</div>

<script>
    $("#delete_post_button").click(function(event) {
        event.preventDefault();
        var postID = $(this).data("postid");

        $.ajax({
            url: '/api/posts/delete',
            type: 'post',
            data: {
                postID: postID,
                blogID: {$blog->id}
            }
        }).done(function (data, textStatus, jqXHR) {
            $("#delete_post_modal").modal('hide');
            refreshData({$blog->id}, 1);
            $("#manage_posts_messages").html('<p class="ui success message">Post deleted</p>');

        }).fail(function (jqXHR, textStatus, errorThrown) {
            data = JSON.parse(jqXHR.responseText);
            $("#manage_posts_messages").html('<p class="ui error message">' + data.errorMessage + '</p>');
        });
    });

    $("#clone_post_button").click(function (event) {
        event.preventDefault();
        var postID = $(this).data("postid");

        $.ajax({
            url: '/api/posts/clone',
            type: 'post',
            data: {
                postID: postID,
                blogID: {$blog->id}
            }
        }).done(function (data, textStatus, jqXHR) {
            $("#delete_post_modal").modal('hide');
            refreshData({$blog->id}, 1);
            $("#manage_posts_messages").html('<p class="ui success message">Post cloned</p>');

        }).fail(function (jqXHR, textStatus, errorThrown) {
            data = JSON.parse(jqXHR.responseText);
            $("#manage_posts_messages").html('<p class="ui error message">' + data.errorMessage + '</p>');
        });
    });

    $("#submit_bulk_action").click(function (event) {
        event.preventDefault();
        $("#bulk_post_modal .action-name").html($('#bulk_action_name').val());
        $("#bulk_post_modal .action-count").html($(".select_post input[type=checkbox]:checked").length);
        $("#bulk_post_modal").modal('setting', 'closable', false).modal('show');
    });

    $("#bulk_post_button").click(function (event) {
        event.preventDefault();

        var action = $('#bulk_action_name').val();
        var postIds = [];

        $(".select_post input[type=checkbox]:checked").each(function() {
            postIds.push($(this).data('post'));
        });

        $.ajax({
            url: '/api/posts/bulkupdate',
            type: 'post',
            data: {
                posts: postIds,
                blogID: {$blog->id},
                action: action
            }
        }).done(function (data, textStatus, jqXHR) {
            $("#bulk_post_modal").modal('hide');
            refreshData({$blog->id}, 1);
            var suffix =  action.slice(-1) === 'e' ? 'd' : 'ed'; 
            $("#manage_posts_messages").html('<p class="ui success message">Post(s) ' + action + suffix + '</p>');
            $("#multi_post_options").hide();

        }).fail(function (jqXHR, textStatus, errorThrown) {
            data = JSON.parse(jqXHR.responseText);
            $("#manage_posts_messages").html('<p class="ui error message">' + data.errorMessage + '</p>');
        });
    });
</script>