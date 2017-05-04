<div class="crumbtrail">
	<a href="/">Home</a><a href='/overview/{$blog.id}'>{$blog.name}</a><a href='/contributors/{$blog.id}'>Contributors</a><a>Add</a>
</div>

<img src="/resources/icons/64/avatar.png" class="settings-icon" />
<h1 class="settings-title" style="margin-top:0px;">Add Contributor<br>
<span class="subtitle">{$blog.name}</span></h1>

<form action="/contributors/{$blog.id}/add/submit" method="POST">
    
	<label for="fld_contributorsearch">User Search</label>
    
    <input type="text" name="fld_contributorsearch" id="fld_contributorsearch" value="" placeholder="Username" />
    <button type="button" id="finduserbutton">Find User</button>
    
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
    
    <label for="fld_privileges">Access Level</label>
    
    <div class="info" style="font-size:80%;">
        <p><strong>Post Only</strong> <i>(Recommended!)</i> - Allows contributor to post new posts and edit their own posts on the blog.</p>
        <p><strong>All</strong> - Gives the contributor full access over the blog, including to edit stylesheet.</p>
    </div>
    
    <select id="fld_privileges" name="fld_privileges">
        <option value="p" selected>Post Only</option>
        <option value="a">All</option>
    </select>
    
    <div class="push-right">
	    <input type="submit" name="fld_submit_contrib" value="Add Contributor" />
        <input type="button" value="Cancel" name="goback" onclick="window.history.back()" />
    </div>
	
</form>