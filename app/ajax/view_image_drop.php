<?php
// Blog ID
if(isset($_GET['blogid'])) $BlogID = sanitize_string($_GET['blogid']);
else die(showError("Could not retrieve blog information"));
?>

<form action="<?= CLIENT_ROOT_BLOGCMS ?>/ajax/drop_image_upload?blogid=<?= $BlogID ?>" class="dropzone" id="newImageUpload" method="post"></form>


<script type="text/javascript" src="<?= CLIENT_ROOT_ABS ?>/resources/js/dropzone.js"></script>
<link rel="stylesheet" type="text/css" href="<?= CLIENT_ROOT_ABS ?>/resources/css/dropzone.css" />

<script>
// Some reason dropzone doesn't load immediately with ajax...
Dropzone.discover();
    
Dropzone.options.newImageUpload = {
    maxFilesize: 20, // MB
    acceptedFiles: 'image/*'
};
</script>

<style>
    .dropzone {
        overflow:auto;
        height:90%;
    }
    #finishButton {
        float:right;
        margin:5px;
    }
</style>

<button id="finishButton" class="action_button" onclick="window.location.reload();">Finish</button>