<style type="text/css">
#upload_area {
    margin:10px;
    padding:10px;
    height:90%;
    overflow:auto;
}
.selectableimage {
    cursor:pointer;
}
</style>

<div id="upload_area">

    <div class="ui tabular menu upload-tabs">
        {foreach from=$tabs key=key item=tab}
            <a class="item {if $key == 0}active{/if}" id="upload_tab_{$key}" data-id="{$key}" {if isset($tab.url)}data-url="{$tab.url}" data-loaded="false"{/if}>{$tab.label}</a>
        {/foreach}
    </div>

    {* This one comes out the box! *}
    <div id="upload_content_0" class="ui segment tab-content">
        <h3>Upload an image</h3>
        <form id='frm_chooseNewImage' method='post' enctype='multipart/form-data' class="ui form">
            <div class="field">
                <label for='file'>Filename</label>
                <input type='file' name='file' id='file' accept="image/png, image/gif, image/jpeg"/>
            </div>
            <div class="push-right">
                <input type='submit' name='image_submit' value='Upload' class="ui button teal">
            </div>
        </form>
    </div>
    <script>
        // Upload a new image (will redirect to a confirmation screen)
        $("#frm_chooseNewImage").submit(function(e) {
            // Make it so that the file can be passed using ajax
            var formData = new FormData();
            formData.append("file", document.getElementById('file').files[0]);
            formData.append("csrf_token", CSRFTOKEN);
            
            // Make the ajax call
            ajax_PostFile("/cms/files/uploadimages/{$blog->id}?replace=1", formData, function(xmlhttp) {
                closeUploadWindow('{$blog->resourcePath()}/images/' + xmlhttp.responseText.trim());
            });
            
            e.preventDefault();

            // Don't redirect
            return false;
        });
    </script>

    {foreach from=$tabs key=key item=tab}
        {if $key > 0}
            <div class="ui segment tab-content" id="upload_content_{$key}" style="display:none;" data-id="{$key}">
                <h3>{$tab.label}</h3>
            </div>
        {/if}
    {/foreach}

    <script>
        $("[id^=upload_tab_").click(function() {
            var tabID = $(this).data('id');
            $("#upload_area .tab-content").hide();
            $("#upload_area .upload-tabs .item").removeClass('active');
            $("#upload_content_" + tabID).show();
            $("#upload_tab_" + tabID).addClass('active');

            var ajaxUrl = $(this).data('url') ?? '';
            var loaded = $(this).data('loaded') ?? false;

            if (ajaxUrl.length > 1 && !loaded) {
                $("#upload_content_" + tabID).html('<img src="/hamlet/images/ajax-loader.gif" alt="Loading...">');

                // Load in content via. Ajax.
                $.get(ajaxUrl, function(data) {
                    $("#upload_content_" + tabID).html(data);
                    $("#upload_tab_" + tabID).data('loaded', true);
                });
            }
        });
    </script>
        
    <script type="text/javascript">        
        function insertAtCursor(myField, myValue) {
            //IE support
            if (document.selection) {
                myField.focus();
                sel = document.selection.createRange();
                sel.text = myValue;
            }
            //MOZILLA and others
            else if (myField.selectionStart || myField.selectionStart == '0') {
                var startPos = myField.selectionStart;
                var endPos = myField.selectionEnd;
                myField.value = myField.value.substring(0, startPos)
                    + myValue
                    + myField.value.substring(endPos, myField.value.length);
            } else {
                myField.value += myValue;
            }
        }
        
        var closeUploadWindow = function(newImageSrc) {

            // Are we appending or replacing the original content?
            {if $returnFormat == "markdown"}
                {if $returnReplace == 1}
                    $("#{$returnElementID}").html("![" + newImageSrc + "](" + newImageSrc + ")");
                {else}
                    insertAtCursor(document.getElementById('{$returnElementID}'), "![" + newImageSrc + "](" + newImageSrc + ")");
                {/if}

            {else if $returnFormat == "html"}
                {if $returnReplace == 1}
                    $("#{$returnElementID}").html("<img src='" + newImageSrc + "' alt='" + newImageSrc + "' />");
                {else}
                    insertAtCursor(document.getElementById('{$returnElementID}'), "<img src='" + newImageSrc + "' alt='" + newImageSrc + "' />");
                {/if}
            {/if}

            // Close window - have no reference to the original object - is it possible to pass it through?
            // $(".rbwindow_screen").remove();
            $('.ui.modal').modal('hide');

            $("html").css("overflow","visible");
            $("body").css("overflow","visible");
            
            return false;
        }
    </script>
</div>