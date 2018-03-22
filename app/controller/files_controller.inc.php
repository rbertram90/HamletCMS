<?php
namespace rbwebdesigns\blogcms;

use rbwebdesigns;
use rbwebdesigns\core\Sanitize;

class FilesController extends GenericController
{
    protected $modelBlogs;
    protected $blog;
    
    public function __construct()
    {
        $this->modelBlogs = BlogCMS::model('\rbwebdesigns\blogcms\model\Blogs');
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
    
    /**
     * Handles /files/manage/<blogid>
     * Setup and run the manage files page
     */
    public function manage(&$request, &$response)
    {
        $blogid = $request->getUrlParameter(1);
        $blog = $this->modelBlogs->getBlogById($blogid);
        
        if(!is_array($blog)) {
            $response->redirect('/', 'Could not find blog', 'error');
        }
        elseif(!$this->modelBlogs->canWrite($blog['id'])) {
            $response->redirect('/', 'Access denied', 'error');
        }

        $imagesDirectory = SERVER_PATH_BLOGS . '/' . $blog['id'] . '/images';
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
        
        $response->setVar('blog', $blog);
        $response->setVar('foldersize', number_format($this->GetDirectorySize($imagesDirectory) / 1000, 2));
        $response->setVar('images', $images);

        $response->addScript('/resources/js/rbwindow.js');
        $response->addScript('/resources/js/rbrtf.js');
        $response->addStylesheet('/resources/css/rbwindow.css');
        $response->addStylesheet('/resources/css/rbrtf.css');
        
        $response->setTitle('Manage Files - ' . $blog['name']);
        $response->write('files/manage.tpl');
    }

    /**
     * Handles /files/delete/<blogid>/<filename>
     * Delete a file by filename
     */
    public function delete(&$request, &$response)
    {
        $blogid = $request->getUrlParameter(1);
        $blog = $this->modelBlogs->getBlogById($blogid);
        
        if(!is_array($blog)) {
            $response->redirect('/', 'Could not find blog', 'error');
        }
        elseif(!$this->modelBlogs->canWrite($blogid)) {
            $response->redirect('/', 'Access denied', 'error');
        }

        $imagesDirectory = SERVER_PATH_BLOGS.'/' . $blogid . '/images';
        $filename = str_replace('_', '.', $request->getUrlParameter(2));
        
        if(!file_exists($imagesDirectory . '/' . $filename)) {
            $response->redirect('/files/manage' . $blog['id'], 'Could not find blog', 'error');
        }
        
        unlink($imagesDirectory . '/' . $filename);
        
        $response->redirect('/files/manage' . $blog['id'], 'File deleted', 'success');
    }
}
