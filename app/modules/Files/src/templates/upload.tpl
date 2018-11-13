<form action="/cms/files/uploadimages/{$blog.id}" class="dropzone" id="newImageUpload" method="post"></form>

<script type="text/javascript" src="/resources/js/dropzone.js"></script>
<link rel="stylesheet" type="text/css" href="/resources/css/dropzone.css" />

<script>
// Some reason dropzone doesn't load immediately with ajax...
Dropzone.discover();
    
Dropzone.options.newImageUpload = {
    maxFilesize: 2, // MB
    acceptedFiles: 'image/*'
};
</script>
