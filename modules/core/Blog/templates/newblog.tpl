<div class="ui grid">
    
    <div class="one column row">
        <div class="column">
            <h1 class="ui header">
                <i class="book icon"></i>
                <div class="content">
                    Create a new blog
                </div>
            </h1>
        </div>
    </div>
    
    <div class="one column row">
        <div class="column">            
            <form action="/cms/blog/create" method="post" class="ui form">

                <div class="ui error message"></div>
                
                <div class="field">
                    <label for="fld_blogname">Blog name</label>
                    <input type="text" name="fld_blogname" id="fld_blogname" size="50" autocomplete="off" />
                </div>

                <div class="field">
                    <label for="fld_blogdesc">Description</label>
                    <textarea name="fld_blogdesc" id="fld_blogdesc" rows="5"></textarea>
                </div>

                <button type="submit" class="ui button labeled icon teal" name="submit_blog"><i class="check icon"></i>Create</button>
                
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