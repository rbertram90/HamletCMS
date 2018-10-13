<form action="/cms/files/uploadimages?blogid={$blog.id}" class="dropzone" id="newImageUpload" method="post"></form>

<script type="text/javascript" src="/resources/js/dropzone.js"></script>
<link rel="stylesheet" type="text/css" href="/resources/css/dropzone.css" />

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