<?php
namespace rbwebdesigns\blogcms\Files\controller;

use rbwebdesigns\blogcms\GenericController;
use rbwebdesigns\blogcms\BlogCMS;
use rbwebdesigns\core\Sanitize;

class Files extends GenericController
{
    protected $modelBlogs;
    protected $blog;
    
    public function __construct()
    {
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\Blogs\model\Blogs');

        $this->blog = BlogCMS::getActiveBlog();
    }

    /* Get the size in bytes of a folder */
    private function getDirectorySize($path){
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
    protected function createFilename($path, $ext)
    {
        $date = new \DateTime();
        $tmpfilename = mt_rand(1000, 9999).$date->getTimestamp().'.'.$ext;
        if(file_exists($path.'/'.$tmpfilename)) $tmpfilename = createFilename($path);
        return $tmpfilename;
    }

    /**
     * Handles /cms/files/manage/<blogid>
     * Setup and run the manage files page
     */
    public function manage(&$request, &$response)
    {        
        if(!is_array($this->blog)) {
            $response->redirect('/cms', 'Could not find blog', 'error');
        }
        elseif(!$this->modelBlogs->canWrite($this->blog['id'])) {
            $response->redirect('/cms', 'Access denied', 'error');
        }

        $imagesDirectory = SERVER_PATH_BLOGS . '/' . $this->blog['id'] . '/images';
        $images = array();
        
        if(is_dir($imagesDirectory)) {
            $files = scandir($imagesDirectory);
            
            foreach($files as $filename) {
                $ext = explode('.', $filename)[1];
                if(strtolower($ext) == 'jpg' || strtolower($ext) == 'png') {
                    $images[] = [
                        'name' => $filename,
                        'size' => number_format(filesize($imagesDirectory.'/'.$filename) / 1000, 2),
                        'date' => date("F d Y", filemtime($imagesDirectory.'/'.$filename)),
                        'file' => str_replace('.', '_', $filename)
                    ];
                }
            }
        }
        BlogCMS::$activeMenuLink = 'files';
        
        $response->setVar('blog', $this->blog);
        $response->setVar('foldersize', number_format($this->getDirectorySize($imagesDirectory) / 1000, 2));
        $response->setVar('images', $images);

        $config = BlogCMS::config();
        $response->setVar('maxfoldersize', $config['files']['upload_bytes_limit'] / 1000000);

        $response->addScript('/resources/js/rbwindow.js');
        $response->addScript('/resources/js/rbrtf.js');
        $response->addStylesheet('/resources/css/rbwindow.css');
        $response->addStylesheet('/resources/css/rbrtf.css');
        
        $response->setTitle('Manage Files - ' . $this->blog['name']);
        $response->write('manage.tpl', 'Files');
    }

    /**
     * Handles /cms/files/delete/<blogid>/<filename>
     * Delete a file by filename
     */
    public function delete(&$request, &$response)
    {
        $blogid = $request->getUrlParameter(1);
        $blog = $this->modelBlogs->getBlogById($blogid);
        
        if(!is_array($blog)) {
            $response->redirect('/cms', 'Could not find blog', 'error');
        }
        elseif(!$this->modelBlogs->canWrite($blogid)) {
            $response->redirect('/cms', 'Access denied', 'error');
        }

        $imagesDirectory = SERVER_PATH_BLOGS.'/' . $blogid . '/images';
        $filename = str_replace('_', '.', $request->getUrlParameter(2));
        
        if(!file_exists($imagesDirectory . '/' . $filename)) {
            $response->redirect('/cms/files/manage' . $blog['id'], 'Could not find blog', 'error');
        }
        
        unlink($imagesDirectory . '/' . $filename);
        
        $response->redirect('/cms/files/manage/' . $blog['id'], 'File deleted', 'success');
    }

    /**
     * Handles /cms/files/viewimagedrop?blogid=<blogid>
     */
    public function viewimagedrop(&$request, &$response)
    {
        $request->isAjax = true;

        if($blogID = $request->getInt('blogid')) {
            $blog = $this->modelBlogs->getBlogById($blogID);
            $response->setVar('blog', $blog);
            $response->write('upload.tpl', 'Files');
        }
    }
    
    /**
     * Handles /cms/files/uploadimage?blogid=<blogid>
     */
    public function uploadimages(&$request, &$response)
    {
        $request->isAjax = true;
        $blog = $this->blog;
        $config = BlogCMS::config();
        $maxDirectorySize = $config['files']['upload_bytes_limit'];
        $maxUploadSize = $config['files']['single_upload_limit'];

        if(!$blog && $blogID = $request->getInt('blogid')) {
            $blog = $this->modelBlogs->getBlogById($blogID);
        }
        if(!$blog) {
            $response->redirect('/cms', 'Blog not found', 'error');
        }
        if(!isset($_FILES) || !array_key_exists('file', $_FILES)) {
            die("You need to select a file. Please reopen link to upload!");
        }
        
        $filetype = strtolower($_FILES["file"]["type"]);
        
        if (!($filetype == "image/gif" || $filetype == "image/jpg" || $filetype == "image/jpeg" || $filetype == "image/pjpeg" || $filetype == "image/png")) {
            die("Unable to continue with upload: Unrecognised file type - $filetype");
        }
        if($_FILES["file"]["size"] > $maxUploadSize) {
            die("Unable to continue with upload: file size too large");
        }
        if ($_FILES["file"]["error"] > 0) {
            die("Unable to continue with upload: ".$_FILES["file"]["error"]."<br />");
        }
        
        $filepath = SERVER_PATH_BLOGS . "/" . $blog['id'];
        
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
        
        $totalfoldersize = $this->getDirectorySize($filepath . '/images');
        
        if($totalfoldersize + $_FILES["file"]["size"] > $maxDirectorySize) {
            die("Unable to continue with upload: total upload limit exceeded!");
        }
        
        $ext = strtoupper(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
        $filename = $this->createFilename($filepath, $ext);
        
        // Save in correct location
        move_uploaded_file($_FILES["file"]["tmp_name"], $filepath . '/images/' . $filename);

        print $filename;
    }

    /**
     * Handles /cms/files/select/<blogid>
     */
    public function fileselect(&$request, &$response)
    {
        // Return Type
        $returnFormat = $request->getString('format', 'markdown');
        // Element ID
        $returnElementID = $request->getString('elemid', 'fld_postcontent');
        // Append or replace
        $returnReplace = $request->getInt('replace', 0);

        $path = SERVER_ROOT."/app/public/blogdata/{$this->blog['id']}/images";

        $request->isAjax = true;
        $response->setVar('blog', $this->blog);
        $response->setVar('showExisiting', is_dir($path));
        $response->setVar('returnReplace', $returnReplace);
        $response->setVar('returnElementID', $returnElementID);
        $response->setVar('returnFormat', $returnFormat);

        $imagesHTML = "";

        if (is_dir($path)) {
            if ($handle = opendir($path)) {
                while (false !== ($file = readdir($handle))) {
                    $ext = strtoupper(pathinfo($file, PATHINFO_EXTENSION));
        
                    if($ext == 'JPG' || $ext == 'PNG' || $ext == 'GIF' || $ext == 'JPEG') {
                        $imagesHTML .= '<img src="/blogdata/'.$this->blog['id'].'/images/'.$file.'" height="100" width="" class="selectableimage" />';
                    }
                }
                closedir($handle);
            }
        }

        $response->setVar('imagesOutput', $imagesHTML);

        $response->write('select.tpl', 'Files');
    }
}