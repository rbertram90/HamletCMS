<div class="ui grid">
    <div class="one column row">
        <div class="column">
            <form method="post" id="frm_updateheader" class="ui form">
                <div class="field">
                    <label>Template</label>
                    <textarea id="ace_edit_view" name="ace_edit_view" rows="20" style="font-family: monospace;">{$headerTemplate}</textarea>
                    <textarea name="header_template" style="display: none;">{$headerTemplate}</textarea>
                    <script>
                        var ace_editor = ace.edit("ace_edit_view");
                        ace_editor.setTheme("ace/theme/textmate");
                        ace_editor.session.setMode("ace/mode/smarty");
                        $(".ace_editor").height('50vh');
                        var textarea = $('textarea[name="header_template"]');
                        ace_editor.getSession().on("change", function () {
                            textarea.val(ace_editor.getSession().getValue());
                        });
                    </script>
                </div>

                <div class="field">
                    <label for="fld_backgroundimage">Background Image</label>
                    <div id="current-profile-image" class="rtfeditor">
                        {if isset($blogconfig['background_image'])}
                            <img src="{$blogconfig.background_image}" style="max-height:100px; max-width:300px;" />
                        {/if}
                    </div>
                    <div style="margin-bottom:20px;">
                        <button type="button" title="Insert Image" class="ui button" id="teaser_image_select">Select New Image</button>
                        <button onclick="return removeImage();" class="ui button">Remove Image</button>
                    </div>
                </div>

                <script>
                    $(document).ready(function() {
                        $("body").append("<div class='ui modal upload_image_modal'></div>");
                    });

                    $('#teaser_image_select').click(function() {
                        $('.ui.upload_image_modal').load('/cms/files/fileselect/{$blog->id}', { 'csrf_token': CSRFTOKEN, 'elemid': 'current-profile-image', 'format': 'html', 'replace': 1 }, function() {
                            $(this).modal('show');
                        });
                    });
                </script>

                <input type="hidden" name="fld_headerbackgroundimage" id="fld_headerbackgroundimage" value="{if isset($blogconfig['background_image'])}{$blogconfig.background_image}{/if}">

                <div class="field">
                    <label for="fld_horizontalposition">Horizontal Position</label>
                    <select name="fld_horizontalposition" id="fld_horizontalposition" class="ui dropdown">
                        <option value="s">Stretch</option>
                        <option value="r">Repeat</option>
                        <option value="n">None</option>
                    </select>
                </div>

                <div id="horizontalalign-wrapper" style="display:none;" class="field">
                    <label for="fld_horizontalalign">Horizontal Alignment</label>
                    <select name="fld_horizontalalign" id="fld_horizontalalign" class="ui dropdown">
                        <option value="l">Left</option>
                        <option value="r">Right</option>
                        <option value="c">Centre</option>
                    </select>
                </div>

                <div class="field">
                    <label for="fld_veritcalposition">Vertical Position</label>
                    <select name="fld_veritcalposition" id="fld_veritcalposition" class="ui dropdown">
                        <option value="s">Stretch</option>
                        <option value="r">Repeat</option>
                        <option value="n">None</option>
                    </select>
                </div>

                <input type="button" class="ui right floated button" value="Cancel" name="goback" onclick="window.history.back()" />
                <input type="submit" class="ui right floated teal button" value="Submit" name="Save" />

            </form>            
        </div>
    </div>
</div>

<script type="text/javascript">    
    {if array_key_exists('bg_image_post_vertical', $blogconfig)}
        $("#fld_veritcalposition").val("{$blogconfig.bg_image_post_vertical}");
    {/if}
    
    {if array_key_exists('bg_image_post_horizontal', $blogconfig)}
        $("#fld_horizontalposition").val("{$blogconfig.bg_image_post_horizontal}");
    {/if}
    
    {if array_key_exists('bg_image_align_horizontal', $blogconfig)}
        $("#fld_horizontalalign").val("{$blogconfig.bg_image_align_horizontal}");
    {/if}
    
    $("#fld_horizontalposition").change(function() {ldelim}
        if($("#fld_horizontalposition").val() == 'n')
        {ldelim}
            $("#horizontalalign-wrapper").show();
        {rdelim}
        else
        {ldelim}
            $("#horizontalalign-wrapper").hide();
        {rdelim}
    {rdelim});
    
    if($("#fld_horizontalposition").val() == 'n')
    {ldelim}
        $("#horizontalalign-wrapper").show();
    {rdelim}
    
    function removeImage()
    {ldelim}
        $("#current-profile-image").html("");
        return false;
    {rdelim}
    
    $("#frm_updateheader").submit(function() {
        // add the new image location to the hidden field
        if(typeof $("#current-profile-image > img").attr("src") == "string") {
            var text = $("#current-profile-image > img").attr("src");
            $("#fld_headerbackgroundimage").attr("value", text);
        }
        else if($("#current-profile-image").html() == '') {
            // Make sure it is actually removed
            $("#fld_headerbackgroundimage").attr("value", "");
        }
    });

    $('.ui.dropdown').dropdown();
</script>
