<?php
namespace rbwebdesigns\blogcms;
use rbwebdesigns;

class FilesController extends GenericController
{
    
    private $modelBlogs;        // Blogs Model
    private $blog;
    protected $view;
    
    public function __construct($cms_db, $view)
    {
        $this->modelBlogs = new ClsBlog($cms_db);      
        $this->view = $view;
    }
    
    public function route($params)
    {
        $blogid = sanitize_number($params[0]);
        
        // Check Permissions
        if(!$this->modelBlogs->canWrite($blogid)) return $this->throwAccessDenied();
        
        // Get blog
        $this->blog = $this->modelBlogs->getBlogById($blogid);
        
        // No blog found!
        if(!is_array($this->blog)) return $this->throwNotFound();
        
        $this->view->setVar('blog', $this->blog);
        
        if(array_key_exists(1, $params))
        {
            switch(sanitize_string($params[1]))
            {
                case 'delete':
                    
                    // Check an filename has been passed in
                    if(!array_key_exists(2, $params)) $this->throwNotFound();
                    
                    // Where the image will be stored
                    $imagesDirectory = SERVER_PATH_BLOGS.'/'.$blogid.'/images';
                    
                    // Restore filetype
                    $filename = str_replace('_', '.', sanitize_string($params[2]));
                    
                    // Check the image exists
                    if(!file_exists($imagesDirectory.'/'.$filename)) $this->throwNotFound();
                    
                    // Perform Delete
                    unlink($imagesDirectory.'/'.$filename);
                    
                    // View Images Page
                    redirect('/files/'.$blogid);
                    
                    break;
                    
                default:
                    $this->manageFiles();
                    break;
            }
        }
        else
        {
            $this->manageFiles();
        }
    }
    
    
    /* Get the size in bytes of a folder */
    private function GetDirectorySize($path){
        $bytestotal = 0;
        $path = realpath($path);
        if($path!==false){
            foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator::SKIP_DOTS)) as $object){
                $bytestotal += $object->getSize();
            }
        }
        return $bytestotal;
    }
    
    
    /* Setup and run the manage files page */
    private function manageFiles()
    {
        $imagesDirectory = SERVER_PATH_BLOGS.'/'.$this->blog['id'].'/images';
        $images = array();
        
        if(is_dir($imagesDirectory))
        {
            $files = scandir($imagesDirectory);
            
            foreach($files as $filename)
            {
                $ext = explode('.', $filename)[1];
                if(strtolower($ext) == 'jpg' or strtolower($ext) == 'png')
                {
                    $images[] = array(
                        'name' => $filename,
                        'size' => number_format(filesize($imagesDirectory.'/'.$filename) / 1000, 2),
                        'date' => date("F d Y", filemtime($imagesDirectory.'/'.$filename)),
                        'file' => str_replace('.', '_', $filename)
                    );
                }
            }
        }
        
        $this->view->setVar('foldersize', number_format($this->GetDirectorySize($imagesDirectory) / 1000, 2));
        
        $this->view->addScript('/resources/js/rbwindow');
        $this->view->addScript('/resources/js/rbrtf');
        $this->view->addStylesheet('/resources/css/rbwindow');
        $this->view->addStylesheet('/resources/css/rbrtf');
        
        $this->view->setVar('images', $images);
        
        $this->view->setPageTitle('Manage Files - '.$this->blog['name']);
        
        $this->view->render('files/manage.tpl');
    }
}
?>