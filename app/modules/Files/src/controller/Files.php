<?php
namespace rbwebdesigns\blogcms\Files\controller;

use rbwebdesigns\blogcms\GenericController;
use rbwebdesigns\blogcms\BlogCMS;
use rbwebdesigns\core\Sanitize;
use rbwebdesigns\core\ImageUpload;

class Files extends GenericController
{
    protected $modelBlogs;
    protected $blog;
    
    public function __construct()
    {
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\Blog\model\Blogs');
        $this->modelContributors = BlogCMS::model('\rbwebdesigns\blogcms\Contributors\model\Contributors');
        $this->blog = BlogCMS::getActiveBlog();

        parent::__construct();
    }

    /**
     * Get the size in bytes of a folder
     */
    private function getDirectorySize($path)
    {
        $bytestotal = 0;
        $path = realpath($path);
        if ($path !== false) {
            foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $object) {
                $bytestotal += $object->getSize();
            }
        }
        return $bytestotal;
    }
    
    /**
     * Create a unique file name
     * 
     * @param string $path directory path
     * @param string $ext file extention
     * 
     * @return string
     */ 
    protected function createFilename($path, $ext)
    {
        $date = new \DateTime();
        $tmpfilename = mt_rand(1000, 9999) . $date->getTimestamp() .'.'. $ext;

        // Check filename generated is unique
        if (file_exists($path .'/'. $tmpfilename)) {
            $tmpfilename = $this->createFilename($path, $ext);
        }

        return $tmpfilename;
    }

    /**
     * Handles /cms/files/manage/<blogid>
     * Setup and run the manage files page
     */
    public function manage(&$request, &$response)
    {
        if(!$blog = BlogCMS::getActiveBlog()) {
            $response->redirect('/cms', 'Could not find blog', 'error');
        }
        elseif(!$this->modelContributors->canWrite($blog->id)) {
            $response->redirect('/cms', 'Access denied', 'error');
        }

        $imagesDirectory = self::getImagesDirectory($blog);
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
        BlogCMS::$activeMenuLink = '/cms/files/manage/'. $blog->id;
        
        $response->setVar('blog', $blog);
        $response->setVar('foldersize', number_format($this->getDirectorySize($imagesDirectory) / 1000000, 2));
        $response->setVar('images', $images);

        $config = BlogCMS::config();
        $response->setVar('maxfoldersize', $config['files']['upload_bytes_limit'] / 1000000);        
        $response->setTitle('Manage Files - ' . $blog->name);
        $response->write('manage.tpl', 'Files');
    }

    /**
     * Handles /cms/files/delete/<blogid>/<filename>
     * Delete a file by filename
     */
    public function delete()
    {
        $blog = BlogCMS::getActiveBlog();
        
        if (is_null($blog)) {
            $this->response->redirect('/cms', 'Could not find blog', 'error');
        }
        elseif (!$this->modelContributors->canWrite($blog->id)) {
            $this->response->redirect('/cms', 'Access denied', 'error');
        }

        $imagesDirectory = SERVER_PATH_BLOGS .'/'. $blog->id .'/images';
        $filename = str_replace('_', '.', $this->request->getUrlParameter(2));
        
        if (!file_exists($imagesDirectory .'/'. $filename)) {
            $this->response->redirect('/cms/files/manage/'. $blog->id, 'Could not find image', 'error');
        }
        
        // Run delete
        unlink($imagesDirectory .'/'. $filename);

        // Delete resized images
        if (file_exists("{$imagesDirectory}/xl/{$filename}")) unlink("{$imagesDirectory}/xl/{$filename}");
        if (file_exists("{$imagesDirectory}/l/{$filename}")) unlink("{$imagesDirectory}/l/{$filename}");
        if (file_exists("{$imagesDirectory}/m/{$filename}")) unlink("{$imagesDirectory}/m/{$filename}");
        if (file_exists("{$imagesDirectory}/s/{$filename}")) unlink("{$imagesDirectory}/s/{$filename}");
        if (file_exists("{$imagesDirectory}/xs/{$filename}")) unlink("{$imagesDirectory}/xs/{$filename}");
        if (file_exists("{$imagesDirectory}/sq/{$filename}")) unlink("{$imagesDirectory}/sq/{$filename}");
        
        $this->response->redirect('/cms/files/manage/'. $blog->id, 'File deleted', 'success');
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
     * Handles /cms/files/uploadimage/<blogid>
     */
    public function uploadimages(&$request, &$response)
    {
        $request->isAjax = true;
        $blog = $this->blog;
        $config = BlogCMS::config();
        $maxDirectorySize = $config['files']['upload_bytes_limit'];
        
        if (!$blog && $blogID = $request->getInt('blogid')) {
            $blog = $this->modelBlogs->getBlogById($blogID);
        }
        if (!$blog) {
            $response->redirect('/cms', 'Blog not found', 'error');
        }
        if (!isset($_FILES) || !array_key_exists('file', $_FILES)) {
            die("You need to select a file. Please reopen link to upload!");
        }

        $imageDirectory = self::getImagesDirectory($blog);
        $totalfoldersize = $this->getDirectorySize($imageDirectory);

        $ext = strtoupper(pathinfo($_FILES["file"]["name"], PATHINFO_EXTENSION));
        $filename = $this->createFilename($imageDirectory, $ext);

        if ($totalfoldersize + $_FILES["file"]["size"] > $maxDirectorySize) {
            die("Unable to continue with upload: total upload limit exceeded!");
        }

        try {
            $upload = new ImageUpload($request->getFile('file'));
            $upload->maxUploadSize = $config['files']['single_upload_limit'];
            $upload->fileTypes = ['image/jpeg','image/png','image/jpg','image/gif','image/pjpeg'];
            $upload->upload($imageDirectory, $filename);

            $blogConfig = $blog->config();
            $filesConfigExists = array_key_exists('files', $blogConfig) && array_key_exists('imagestyles', $blogConfig['files']);

            if ($filesConfigExists) {
                $sizes = $blogConfig['files']['imagestyles'];
            }
            else {
                $sizes = FileSettings::getDefaultImageSizes();
            }

            // Create thumbnails
            $upload->createThumbnail($imageDirectory . '/xl', null, $sizes['xl']['w'], $sizes['xl']['h']);
            $upload->createThumbnail($imageDirectory . '/l', null, $sizes['l']['w'], $sizes['l']['h']);
            $upload->createThumbnail($imageDirectory . '/m', null, $sizes['m']['w'], $sizes['m']['h']);
            $upload->createThumbnail($imageDirectory . '/s', null, $sizes['s']['w'], $sizes['s']['h']);
            $upload->createThumbnail($imageDirectory . '/xs', null, $sizes['xs']['w'], $sizes['xs']['h']);
            $upload->createThumbnail($imageDirectory . '/sq', null, $sizes['sq']['w'], $sizes['sq']['h']);
        }
        catch (\Exception $e) {
            die($e->getMessage());
        }
        
        print $filename;
    }

    /**
     * @return string path to blog images folder
     */
    public static function getImagesDirectory($blog)
    {
        $filepath = SERVER_PATH_BLOGS ."/". $blog->id;
        
        if (!is_dir($filepath .'/images')) {
            if (is_dir($filepath)) {
                mkdir($filepath .'/images');
            }
            else {
                die("Unable to continue with upload: Error finding blog directory - please contact the system administrator");
            }
        }
        
        return $filepath .'/images';
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

        $path = SERVER_PUBLIC_PATH. "/blogdata/{$this->blog->id}/images";

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
                        $imagesHTML .= '<img src="/blogdata/'. $this->blog->id .'/images/'. $file .'" height="100" width="" class="selectableimage">';
                    }
                }
                closedir($handle);
            }
        }

        $response->setVar('imagesOutput', $imagesHTML);

        $response->write('select.tpl', 'Files');
    }
}
