<div id="showExisting">
    <h3>Choose from Existing Photos</h3>
    <form id='frm_chooseExistingImage'>
    
    <input type="hidden" value="" name="fld_choosenimage" id="fld_choosenimage" />

    {$imagesOutput}

    <input type='submit' value='Choose Image' name='btn_chooseImage' id='btn_chooseImage' />
    </form>
</div>

<script type="text/javascript">
    // Show and hide the different tabs
    $(".selectableimage").click(function() {
        $(".selectableimage").css('border','0');
        $(this).css('border','4px solid green');
        $("#fld_choosenimage").val($(this).attr('src'));
    });
    
    // Insert an existing image into the post
    $("#frm_chooseExistingImage").submit(function(e) {
        e.preventDefault();
        if($("#fld_choosenimage").val() === "") {
            alert("Please select an image");
            return false;
        }
        return closeUploadWindow($("#fld_choosenimage").val());
    });
</script>