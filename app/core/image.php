<?php
namespace rbwebdesigns;

class ImageProcessor {
    
	private $maxUploadSize;
	
    public function __construct() {
        $this->maxUploadSize = 1000000;
    }
    
    // Create thumbnail (current image, new image folder)
    // createThumb($user_image_folder."/".$_FILES["file"]["name"], $user_image_folder."/square/", 300, 300);
    public function createThumbnail($psImagePath, $psDestinationPath, $piWidth = 300, $piHeight = 300) {
        
        // Actual Image Size
        list($imageHeight, $imageWidth) = getimagesize($psImagePath);
        
        // Thumbnail Size
        list($thumbHeight, $thumbWidth) = array($piHeight, $piWidth);
        
        // Create full size Image from file path
        $srcImage = imagecreatefromjpeg($psImagePath);
        
        $lsTargetDir = pathinfo($psDestinationPath, PATHINFO_DIRNAME);
                
        // Create the directory structure if not already in place
        if(!is_dir($lsTargetDir) && strlen($lsTargetDir) > 0) mkdir($lsTargetDir);
        
        // Find the smallest of the two
        $min = min(array($imageHeight, $imageWidth));
        
        // Calculate how to resize the image to a (smaller?) one
        if($min == $imageWidth) {
            $startY = 0;
            $startX = floor(($imageHeight - $imageWidth) / 2);
        }
        else {
            $startX = 0;
            $startY = floor(($imageWidth - $imageHeight) / 2);
        }
        
        // Create thumbnail
        $destImage = imagecreatetruecolor($thumbWidth, $thumbHeight);
        
        //imagecopy($destImage, $srcImage, 0, 0, $startX, $startY, $min, $min);
        imagecopyresampled($destImage, $srcImage, 0, 0, $startX, $startY, $thumbWidth, $thumbHeight, $min, $min);
        
        // Output to file
        imagejpeg($destImage, $psDestinationPath);
        
        // Create return information
        $larrUpload = array();
        $larrUpload['filename'] = $psDestinationPath;
        $larrUpload['filepath'] = $psDestinationPath;
        $larrUpload['filesize'] = getimagesize($psDestinationPath);
        $larrUpload['fileerror'] = '';
        
        // Output to screen?
        // echo "<p><img src='$destLoc' alt='thumbnail' /></p>";
        return $larrUpload;
    }
    
	/**
	Example Input for multiple file upload
	Array (
		[upload] => Array ( 
			[name] => Array ( [0] => map_v3-Optimized.png [1] => ttd_small.png ) 
			[type] => Array ( [0] => image/png [1] => image/png ) 
			[tmp_name] => Array ( [0] => C:\xampp\tmp\php3AC2.tmp [1] => C:\xampp\tmp\php3AC3.tmp ) 
			[error] => Array ( [0] => 0 [1] => 0 ) 
			[size] => Array ( [0] => 179949 [1] => 23277 ) 
		)
	)
	**/
    public function multiUpload($files) {
        // To do...
		// <input name="upload[]" type="file" multiple="multiple" />		
		echo "<pre>".print_r($_FILES)."</pre>";
    }
    
    public function createFileName($psPrefix = "", $psSuffix = "") {
        $lsFileName = $psPrefix.rand(10000,32000).rand(10000,32000).$psSuffix.'.'.pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION);
        return strtolower($lsFileName);
    }
    
    public function upload($psFolderPath) {

        $larrUpload = array();
        
        if($_FILES['file']['type'] !== 'image/jpeg') {
            // Currently only support JPG
            $larrUpload['fileerror'] = 'Unable to upload - We only support .JPG images right now';
            return $larrUpload;
            
        } if($_FILES['file']['size'] > $this->maxUploadSize) {
            // File too large
            $larrUpload['fileerror'] = 'Unable to upload - Maximum file size is 200KB';
            return $larrUpload;
            
        } if($_FILES['file']['error'] > 0) {
            // Another error has occured
            $larrUpload['fileerror'] = 'Unable to upload - Error has Occurred Code: '.$_FILES['file']['error'];
            return $larrUpload;
        }
        
        // Create the directory structure if not already in place
        if(!is_dir($psFolderPath) && strlen($psFolderPath) > 0) mkdir($psFolderPath, 0777, true);
        
        // Make a new file name (to hopefully avoid duplicates)
        $_FILES['file']['name'] = $this->createFileName();
        
        // loop until we find a unique name
        while(file_exists($psFolderPath.$_FILES['file']['name'])) {
            // Regenerate another random number
            $_FILES['file']['name'] = $this->createFileName();
        }
        
        // Move to final resting place
        move_uploaded_file($_FILES['file']['tmp_name'], $psFolderPath.'/'.$_FILES['file']['name']);
        
        // Create return information
        $larrUpload['filename'] = $_FILES['file']['name'];
        $larrUpload['filepath'] = $psFolderPath.'/'.$_FILES['file']['name'];
        $larrUpload['filesize'] = $_FILES['file']['size'];
        $larrUpload['fileerror'] = '';
        
        // Success
        return $larrUpload;
    }
}
?>