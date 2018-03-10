<?php
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
function createFilename($path, $ext)
{
    $date = new DateTime();
    $tmpfilename = mt_rand(1000, 9999).$date->getTimestamp().'.'.$ext;
    if(file_exists($path.'/'.$tmpfilename)) $tmpfilename = createFilename($path);
    return $tmpfilename;
}

// --- Main ---
// Note - the user has access to upload - this has already been checked...

// This file cannot be accessed directly...
if(!isset($_SESSION['userid']))
{
    http_response_code(500);
    die('Session Not Started');
}

// Check the blog id was passed in
if(isset($_GET['blogid']))
{
    $BlogID = safeString($_GET['blogid']);
}
else
{
    http_response_code(500);
    die("Could not retrieve blog information");
}

if(!isset($_FILES) || !array_key_exists('file', $_FILES))
{
    http_response_code(500);
    die("You need to select a file. Please reopen link to upload!");
}

$filetype = strtolower($_FILES["file"]["type"]);

// If file type is correct type and is less than 20kb
if (!($filetype == "image/gif" || $filetype == "image/jpg" || $filetype == "image/jpeg" || $filetype == "image/pjpeg" || $filetype == "image/png")) {
    http_response_code(500);
    die("Unable to continue with upload: Unrecognised file type");
}

if($_FILES["file"]["size"] > 2000000)
{
    http_response_code(500);
    die("Unable to continue with upload: File too large - max size = 2MB");
}

// then if file has upload error then return error
if ($_FILES["file"]["error"] > 0)
{
    http_response_code(500);
    die("Unable to continue with upload: ".$_FILES["file"]["error"]."<br />");
}

// Setup the images folder if needbe
$filepath = SERVER_ROOT."/data/blogs/$BlogID";

if(!is_dir($filepath.'/images'))
{
    // No Images directory exists
    if(is_dir($filepath))
    {
        // We're in the correct place - make a new folder
        mkdir($filepath.'/images');
    }
    else
    {
        http_response_code(500);
        // Not convinced we're in the right place - throw an error.
        die("Unable to continue with upload: Unable to find blog directory - please contact the system administrator");
    }
}

$totalfoldersize = GetDirectorySize($filepath.'/images');

if($totalfoldersize + $_FILES["file"]["size"] > 50000000) {
    http_response_code(500);
    die("Unable to continue with upload: 50 MB total upload limit exceeded!");
}

$ext = strtoupper(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
$filename = createFilename($filepath, $ext);

// Save in correct location
move_uploaded_file($_FILES["file"]["tmp_name"], $filepath.'/images/'.$filename);

?>