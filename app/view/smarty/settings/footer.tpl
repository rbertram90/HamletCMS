<div class="ui grid">
    <div class="one column row">
        <div class="column">
            {viewCrumbtrail(array("/blog/overview/{$blog['id']}", $blog['name'], "/settings/menu/{$blog['id']}", 'Settings'), 'Customise Footer')}
        </div>
    </div>
    <div class="one column row">
        <div class="column">
            {viewPageHeader('Customise Footer', 'footer.png', $blog['name'])}

            <form action="/settings/footer/{$blog.id}" method="post" id="frm_updatefooter" class="ui form">

                <div class="field">
                    <label for="fld_numcolumns">Number of Columns</label>
                    <select name="fld_numcolumns" id="fld_numcolumns">
                        <option value="1">1</option>
                        <option value="2">2</option>
                    </select>
                </div>
                
                <div class="field">
                    <label for="fld_contentcol1">Content - Column 1</label>
                    <textarea name="fld_contentcol1" id="fld_contentcol1">{strip}
                        {if isset($blogconfig['content_col1'])}
                            {$blogconfig.content_col1}
                        {/if}
                    {/strip}</textarea>
                </div>
                
                <div class="field">
                    <div id="wrap_cc2">
                        <label for="fld_contentcol2">Content - Column 2</label>
                        <textarea name="fld_contentcol2" id="fld_contentcol2">{strip}
                            {if isset($blogconfig['content_col2'])}
                                {$blogconfig.content_col2}
                            {/if}
                        {/strip}</textarea>
                    </div>
                </div>
                
                <div class="field">
                    <label for="fld_backgroundimage">Background Image</label>
                    <div id="current-profile-image" class="rtfeditor">
                    {if isset($blogconfig['background_image'])}
                        <img src="{$blogconfig.background_image}" style="max-height:100px; max-width:300px;" />
                    {/if}
                    </div>
                </div>

            <!--
                <button type="button" title="Insert Image" onclick="rbrtf_addImage('<?=$arrayBlog['id']?>'); return false;">Select New Image</button>
            -->
                <div class="field">
                    <button type="button" class="ui button" title="Insert Image" onclick="rbrtf_showWindow('{$clientroot_blogcms}/ajax/add_image?blogid={$blog.id}&format=html&elemid=current-profile-image&replace=1');">Select New Image</button>

                    <button type="button" class="ui button" onclick="removeImage();">Remove Image</button>

                    <input type="hidden" name="fld_footerbackgroundimage" id="fld_footerbackgroundimage" value="{if isset($blogconfig['background_image'])}{$blogconfig.background_image}{/if}" />
                </div>
                    
                <div class="field">
                    <label for="fld_horizontalposition">Horizontal Position</label>
                    <select name="fld_horizontalposition" id="fld_horizontalposition">
                        <option value="s">Stretch</option>
                        <option value="r">Repeat</option>
                        <option value="n">None</option>
                    </select>
                </div>
                
                <div class="field">
                    <label for="fld_horizontalposition">Vertical Position</label>
                    <select name="fld_veritcalposition" id="fld_veritcalposition">
                        <option value="s">Stretch</option>
                        <option value="r">Repeat</option>
                        <option value="n">None</option>
                    </select>
                </div>

                <input type="button" class="ui button right floated" value="Cancel" name="goback" onclick="window.history.back()" />
                <input type="submit" class="ui button teal right floated" value="Submit" name="Save" />
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
    
    function removeImage()
    {ldelim}
        $("#current-profile-image").html("");
        return false;
    {rdelim}

    var checkFields = function ()
    {ldelim}
        if($("#fld_numcolumns").val() == 1)
        {ldelim}
            $("#wrap_cc2").hide();
        {rdelim}
        else
        {ldelim}
            $("#wrap_cc2").show();
        {rdelim}
    {rdelim};
    
    {if isset($blogconfig['numcols']) and $blogconfig['numcols'] == '2'}
        $("#fld_numcolumns").val(2);
    {/if}
    
    $("#fld_numcolumns").change(checkFields);
    // Init
    checkFields();
    
    
    $("#frm_updatefooter").submit(function()
    {ldelim}
        // add the new image location to the hidden field
        //var newimagesrc = ;
        if(typeof $("#current-profile-image > img").attr("src") == "string")
        {ldelim}
            var text = $("#current-profile-image > img").attr("src");
            $("#fld_footerbackgroundimage").attr("value", text);
        {rdelim}
        else if($("#current-profile-image").html() == '')
        {ldelim}
            // Make sure it is actually removed
            $("#fld_footerbackgroundimage").attr("value", "");
        {rdelim}
        //$("#fld_footerbackgroundimage").val("" + newimagesrc);
        //return false;
    {rdelim});
</script>