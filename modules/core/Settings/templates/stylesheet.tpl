<div class="ui grid">
    <div class="one column row">
        <div class="column">            
            <form method="POST" class="ui form">

                <div class="field">
                    <label for="fld_css">CSS</label>

                    <div class="ui segment secondary">
                        <i style="font-weight:normal;">Not sure what CSS is? Check out the CSS tutorial over at <a href="https://www.codecademy.com/learn/learn-css" target="_blank">Codecademy</a></i>
                    </div>
                    
                    <textarea id="ace_edit_view" name="ace_edit_view" rows="20" style="font-family: monospace;">{strip}
                        {file_get_contents("{$serverroot}/{$blog->id}/default.css")}
                    {/strip}</textarea>
                    <textarea name="fld_css"  id="fld_css" style="display: none;">{strip}
                        {file_get_contents("{$serverroot}/{$blog->id}/default.css")}
                    {/strip}</textarea>
                    <script>
                        var ace_editor = ace.edit("ace_edit_view");
                        ace_editor.setTheme("ace/theme/textmate");
                        ace_editor.session.setMode("ace/mode/css");
                        $(".ace_editor").height('50vh');
                        var textarea = $('textarea[name="fld_css"]');
                        ace_editor.getSession().on("change", function () {
                            textarea.val(ace_editor.getSession().getValue());
                        });
                    </script>
                </div>

                <input type="button" class="ui button right floated" value="Cancel" name="goback" onclick="window.history.back()" />
                <input type="submit" class="ui button teal right floated" name="submit_update" value="Save" />

            </form>
        </div>
        
    </div>
</div>