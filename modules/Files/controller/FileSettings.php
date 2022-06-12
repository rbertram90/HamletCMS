<?php
namespace HamletCMS\Files\controller;

use HamletCMS\GenericController;
use HamletCMS\HamletCMS;
use rbwebdesigns\core\ImageUpload;

class FileSettings extends GenericController
{
    /** @var \HamletCMS\Blog\Blog */
    protected $blog;

    public function __construct()
    {
        parent::__construct();

        $this->modelPermissions = HamletCMS::model('\HamletCMS\Contributors\model\Permissions');
        $this->blog = HamletCMS::getActiveBlog();

        if (!$this->modelPermissions->userHasPermission('change_settings', $this->blog->id)) {
            $this->response->redirect('/', '403 Access Denied', 'error');
        }

        HamletCMS::$activeMenuLink = '/cms/settings/menu/'. $this->blog->id;
    }

    public function getImageSizeSettings()
    {
        $config = $this->blog->config();

        if (is_null($config) || !array_key_exists('files', $config)) {
            return [
                'imagestyles' => self::getDefaultImageSizes()
            ];
        }

        return $config['files'];
    }

    public function settings()
    {
        if ($this->request->method() == 'POST') {
            return $this->saveSettings();
        }

        $this->response->setBreadcrumbs([
            $this->blog->name => $this->blog->url(),
            'Settings' => "/cms/settings/menu/{$this->blog->id}",
            'Files' => null
        ]);
        $this->response->headerIcon = 'sliders horizontal';
        $this->response->headerText = $this->blog->name . ': File settings';

        $this->response->setVar('blog', $this->blog);
        $this->response->setVar('config', $this->getImageSizeSettings());
        $this->response->setTitle('Manage file settings');
        $this->response->write('settings.tpl', 'Files');
    }

    protected function saveSettings()
    {
        $fileConfig = $this->getImageSizeSettings();
        $imageSizes = $fileConfig['imagestyles'];

        // Process other thumbnail sizes
        foreach ($imageSizes as $imageSize) {
            $width = $this->request->getInt($imageSize .'_image_width');
            $height = $this->request->getInt($imageSize .'_image_height');

            $validWidth = is_int($width) && $width > 0;
            $validHeight = is_int($height) && $height > 0;

            // Have the values changed?
            if ($validWidth && $validHeight && ($imageSizes[$imageSize]['w'] != $width || $imageSizes[$imageSize]['h'] != $height)) {
                $imageSizes[$imageSize] = ['w' => $width, 'h' => $height];

                // Re-create the thumbnails for this size
                $this->regenerateThumbnails($imageSize, $width, $height);
            }
        }

        $defaultSize = $this->request->getString('default_image_size', 'square');
        if (!in_array($defaultSize, array_keys($imageSizes))) {
            $defaultSize = 'square';
        }

        $config = ['files' => ['imagestyles' => $imageSizes, 'defaultsize' => $defaultSize]];
        $this->blog->updateConfig($config);
        $this->response->redirect('/cms/files/settings/'. $this->blog->id, 'File settings updated', 'success');
    }

    public function addSize()
    {
        $name = $this->request->getString('new_image_name');
        $name = preg_replace("/[^a-zA-Z0-9]+/", "", $name);
        $name = strtolower($name);

        if (strlen($name) === 0) {
            $this->response->redirect('/cms/files/settings/'. $this->blog->id, 'Unable to add new size, name invalid.', 'error');
        }

        $width = $this->request->getInt('new_image_width');
        $height = $this->request->getInt('new_image_height');

        if ($width <= 0 || $height <= 0 || $width > 3000 || $height > 3000) {
            $this->response->redirect('/cms/files/settings/'. $this->blog->id, 'Unable to add new size, dimentions invalid.', 'error');
        }

        $imageSizes = $this->getImageSizeSettings()['imagestyles'];
        $imageSizes[$name] = ['w' => $width, 'h' => $height];

        $this->blog->updateConfig(['files' => ['imagestyles' => $imageSizes]]);
        $this->regenerateThumbnails($name, $width, $height);
        $this->response->redirect('/cms/files/settings/'. $this->blog->id, 'File settings updated', 'success');
    }

    public function removeSize()
    {
        $name = $this->request->getString('sizeName');
        $imageSizes = $this->getImageSizeSettings()['imagestyles'];

        if (!isset($imageSizes[$name])) {
            $this->response->setBody('{ "success": false, "errorMessage": "No image size found" }');
            $this->response->code(400);
            return;
        }
        elseif ($name === 'square') {
            $this->response->setBody('{ "success": false, "errorMessage": "This size cannot be deleted." }');
            $this->response->code(400);
            return;
        }

        $imagesDirectory = Files::getImagesDirectory($this->blog) . '/' . $name;

        if (is_dir($imagesDirectory)) {
            // Remove Files
            $it = new \RecursiveDirectoryIterator($imagesDirectory, \RecursiveDirectoryIterator::SKIP_DOTS);
            $files = new \IteratorIterator($it);

            /** @var \SplFileInfo $file */
            foreach ($files as $file) {
                if (!unlink($file->getRealPath())) {
                    $this->response->setBody('{ "success": false, "errorMessage": "Failed to delete '. $file->getFilename() .'" }');
                    $this->response->code(500);
                    return;
                }
            }

            if (!rmdir($imagesDirectory)) {
                $this->response->setBody('{ "success": false, "errorMessage": "Failed to delete images directory." }');
                $this->response->code(500);
                return;
            }
        }

        $config = $this->blog->config();
        unset($config['files']['imagestyles'][$name]);

        if (!$this->blog->overwriteConfig($config)) {
            $this->response->setBody('{ "success": false, "errorMessage": "Images deleted, config failed to save." }');
            $this->response->code(500);
            return;
        }

        $this->response->setBody('{ "success": true }');
        $this->response->code(200);
    }

    public static function getDefaultImageSizes()
    {
        return [
            'large'  => ['w' => 1600, 'h' => 1200],
            'medium' => ['w' => 800,  'h' => 600],
            'small'  => ['w' => 320,  'h' => 240],
            'square' => ['w' => 180,  'h' => 180],
        ];
    }

    protected function regenerateThumbnails($name, $width, $height)
    {
        $imagesDirectory = Files::getImagesDirectory($this->blog);

        if (is_dir($imagesDirectory)) {
            $files = scandir($imagesDirectory);
        }
        else {
            return false;
        }

        // Re-create the thumbnails for this size
        if (isset($files)) {
            foreach ($files as $filename) {
                $ext = explode('.', $filename)[1];
                if (strtolower($ext) == 'jpg' || strtolower($ext) == 'png') {
                    $upload = new ImageUpload([
                        'type' => 'image/' . strtolower($ext),
                        'new_path' => $imagesDirectory .'/'. $filename
                    ]);
                    $upload->createThumbnail($imagesDirectory .'/'. $name, null, $width, $height);
                }
            }
        }
        else {
            return false;
        }
    }
}