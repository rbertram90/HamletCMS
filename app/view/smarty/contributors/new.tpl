<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/overview/{$blog['id']}", "{$blog['name']}", "/contributors/{$blog['id']}", 'Contributors'), 'Add')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('Add Contributor', 'avatar.png', "{$blog['name']}")}
        </div>
    </div>

    <div class="one column row">
        <div class="column">
            <form action="/contributors/{$blog.id}/add/submit" method="POST" class="ui form">
                <div class="inline field">
                   <label for="fld_contributorsearch">User Search</label>
                    <input type="text" name="fld_contributorsearch" id="fld_contributorsearch" value="" placeholder="Username" />
                    <button type="button" id="finduserbutton" class="ui button">Find User</button>
                </div>

                <input type="hidden" name="fld_contributor" id="fld_contributor" value="" />

                <div id="usersearchresults" style="background-color:#fff; border:1px solid #eee;"></div>

                <script>
                $("#finduserbutton").click(function() {

                    var searchvalue = $("input[name=fld_contributorsearch]").val();

                    if(searchvalue.length < 2)
                    {
                        $("#usersearchresults").html("<span style='color:red;'>Please enter a username longer than 2 characters</span>");
                        return;
                    } 

                    $.ajax({
                      type: "POST",
                      url: "/ajax/finduser",
                      data: {
                          searchterm: searchvalue
                      },
                      success: function(data) {
                          $("#usersearchresults").html(data);
                      }
                    });
                });
                </script>

                <div class="inline field">

                <label for="fld_privileges">Access Level</label>

                <div class="ui secondary segment">
                    <p><strong>Post Only</strong> <i>(Recommended!)</i> - Allows contributor to post new posts and edit their own posts on the blog.</p>
                    <p><strong>All</strong> - Gives the contributor full access over the blog, including to edit stylesheet.</p>
                </div>

                <select id="fld_privileges" name="fld_privileges">
                    <option value="p" selected>Post Only</option>
                    <option value="a">All</option>
                </select>
                </div>



                <input type="button" value="Cancel" name="goback" class="ui right floated button" onclick="window.history.back()" />
                <input type="submit" name="fld_submit_contrib" class="ui button right floated teal" value="Add Contributor" />
            </form>
        </div>
    </div>
</div>