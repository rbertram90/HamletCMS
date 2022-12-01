<div class="ui grid">
    <div class="one column row">
        <div class="column">
            <form method="POST" class="ui form" id="comments_settings_form">
                <div class="field">
                    <label for="ace_edit_view">Comment template</label>
                    <textarea id="ace_edit_view" name="ace_edit_view" rows="20" style="font-family: monospace;">{$commentTemplate}</textarea>
                    <textarea name="comment_template" style="display: none;">{$commentTemplate}</textarea>
                    <script>
                        var ace_editor = ace.edit("ace_edit_view");
                        ace_editor.setTheme("ace/theme/textmate");
                        ace_editor.session.setMode("ace/mode/smarty");
                        $(".ace_editor").height('30vh');
                        var textarea = $('textarea[name="comment_template"]');
                        ace_editor.getSession().on("change", function () {
                            textarea.val(ace_editor.getSession().getValue());
                        });
                    </script>
                </div>
                <!--
                <div class="inline field">
                    <div class="ui checkbox">
                        <input type="checkbox" name="comment_moderation_enabled" id="comment_moderation_enabled">
                        <label for="comment_moderation_enabled">Enable comment moderation</label>
                    </div>
                </div>
                -->
                <button class="ui teal labeled icon button"><i class="save icon"></i> Save</button>
            </form>
        </div>
    </div>
</div>