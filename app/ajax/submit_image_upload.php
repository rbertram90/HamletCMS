<?php
header('Access-Control-Allow-Origin: *');

function GetDirectorySize($path){
    $bytestotal = 0;
    $path = realpath($path);
    if($path!==false){
        foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $object){
            $bytestotal += $object->getSize();
        }
    }
    return $bytestotal;
}

// Create a unique filename
function createFilename($path, $ext) {
    $date = new DateTime();
    $tmpfilename = mt_rand(1000, 9999).$date->getTimestamp().'.'.$ext;
    if(file_exists($path.'/'.$tmpfilename)) $tmpfilename = createFilename($path);
    return $tmpfilename;
}

// Require access to the model
// require_once dirname(__FILE__).'/ajax_setup.inc.php';

if(isset($_GET['blogid'])) {
    $lsBlogID = safeString($_GET['blogid']);
} else {
    die(showError("Could not retrieve blog information"));
}

if(!isset($_FILES) || !array_key_exists('file', $_FILES)) {
    die("You need to select a file. Please reopen link to upload!");
}

$filetype = strtolower($_FILES["file"]["type"]);

// If file type is correct type and is less than 20kb
if (!($filetype == "image/gif" || $filetype == "image/jpg" || $filetype == "image/jpeg" || $filetype == "image/pjpeg" || $filetype == "image/png")) {
    die("Unable to continue with upload: Unrecognised file type - $filetype");
}

// 1KB = 1000
// 1MB = 1000000
// 50MB = 50000000

if($_FILES["file"]["size"] > 2000000) {
    die("Unable to continue with upload: File too large - max size = 2MB");
}

// then if file has upload error then return error
if ($_FILES["file"]["error"] > 0) {
    die("Unable to continue with upload: ".$_FILES["file"]["error"]."<br />");
}

// Setup the images folder if needbe
$filepath = SERVER_PATH_BLOGS . "/$lsBlogID";

if(!is_dir($filepath.'/images')) {
    // No Images directory exists
    if(is_dir($filepath)) {
        // We're in the correct place - make a new folder
        mkdir($filepath.'/images');
    } else {
        // Not convinced we're in the right place - throw an error.
        die("Unable to continue with upload: Error finding blog directory - please contact the system administrator");
    }
}

$totalfoldersize = GetDirectorySize($filepath.'/images');

if($totalfoldersize + $_FILES["file"]["size"] > 50000000) {
    die("Unable to continue with upload: 50 MB total upload limit exceeded!");
}

$ext = strtoupper(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
$filename = createFilename($filepath, $ext);

// Save in correct location
move_uploaded_file($_FILES["file"]["tmp_name"], $filepath.'/images/'.$filename);

?>

<h3>File Uploaded Successful</h3>

<form id="frm_uploadedImage" method="post" name="frm_uploadedImage" onsubmit="closeUploadWindow('/blogdata/<?=$lsBlogID?>/images/<?=$filename?>'); return false;">
    <div class="push-right">
        <input type="submit" value="Close and Add Image to Post" name="fld_submit" />
    </div>
</form>