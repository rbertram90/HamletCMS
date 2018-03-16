<div class="ui grid">
    
    <div class="one column row">
        <div class="column">
            <img src="/resources/icons/64/add_doc.png" class="settings-icon" /><h1 class="settings-title">Create a new blog</h1>
        </div>
    </div>
    
    <div class="one column row">
        <div class="column">            
            <form action="/blog/create" method="post" class="ui form">

                <div class="ui error message"></div>
                
                <div class="field">
                    <label for="fld_blogname">Blog Name</label>
                    <input type="text" name="fld_blogname" id="fld_blogname" size="50" autocomplete="off" />
                </div>

                <div class="field">
                    <label for="fld_blogdesc">Description</label>
                    <textarea name="fld_blogdesc" id="fld_blogdesc" rows="5"></textarea>
                </div>

                <input type="submit" class="ui button teal" name="submit_blog" value="Submit" />
                
                <input type="text" name="fld_generic" id="fld_generic" class="nobots" />
                
            </form>
            
            <script>
            $('.ui.form').form({
                fields: {
                  fld_blogname : 'empty'
                }
            });
            </script>
            
            
            <style>.nobots { visibility: hidden; }</style>
        </div>
    </div>
</div>