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

    {if $showExisiting}

    <div class="ui buttons">
        <button id="linkShowNew" type="button" class="ui button">Upload New</button>
        <div class="or"></div>
        <button id="linkShowExisting" type="button" class="ui button">Choose Existing</button>
    </div>
    
    <div id="showExisting" class="ui segment">
        <h3>Choose from Existing Photos</h3>
        <form id='frm_chooseExistingImage'>
        
        <input type="hidden" value="" name="fld_choosenimage" id="fld_choosenimage" />

        {$imagesOutput}

        <input type='submit' value='Choose Image' name='btn_chooseImage' id='btn_chooseImage' />
        </form>
    </div>
    
    <script type="text/javascript">
        // Show and hide the different tabs
        $("#showExisting").hide();
        $("#linkShowNew").click(function() {
            $("#showExisting").hide();
            $("#showNew").show();
        });
        $("#linkShowExisting").click(function() {
            $("#showExisting").show();
            $("#showNew").hide();
        });
        $(".selectableimage").click(function() {
            $(".selectableimage").css('border','0');
            $(this).css('border','4px solid green');
            $("#fld_choosenimage").val($(this).attr('src'));
        });
        
        // Insert an existing image into the post
        $("#frm_chooseExistingImage").submit(function() {
            if($("#fld_choosenimage").val() === "") {
                alert("Please select an image");
                return false;
            }
            return closeUploadWindow($("#fld_choosenimage").val());
        });
    </script>
    
    {/if}
    
    <div id="showNew" class="ui segment">
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
    
    <script type="text/javascript">
        // Upload a new image (will redirect to a confirmation screen)
        $("#frm_chooseNewImage").submit(function(e) {
            // Make it so that the file can be passed using ajax
            var formData = new FormData();
            formData.append("file", document.getElementById('file').files[0]);
            formData.append("csrf_token", CSRFTOKEN);
            
            // Make the ajax call
            ajax_PostFile("/cms/files/uploadimages/{$blog->id}?replace=1", formData, function(xmlhttp) {
                closeUploadWindow('/blogdata/{$blog->id}/images/' + xmlhttp.responseText.trim());
            });
            
            e.preventDefault();

            // Don't redirect
            return false;
        });
        
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

               {elseif $returnFormat == "html"}
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