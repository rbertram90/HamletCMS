<?php
namespace HamletCMS\Files\controller;

use HamletCMS\GenericController;
use HamletCMS\HamletCMS;
use rbwebdesigns\core\ImageUpload;

class FileSettings extends GenericController
{

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

    public function settings()
    {
        $config = $this->blog->config();

        if (is_null($config) || !array_key_exists('files', $config)) {
            $fileConfig = [
                'imagestyles' => self::getDefaultImageSizes()
            ];
        }
        else {
            $fileConfig = $config['files'];
        }

        if ($this->request->method() == 'POST') {

            $imageSizes = $fileConfig['imagestyles'];
            $imagesDirectory = Files::getImagesDirectory($this->blog);

            if (is_dir($imagesDirectory)) {
                $files = scandir($imagesDirectory);
            }

            $width = $this->request->getInt('sq_image_width');

            // Process square thumbnail
            if (is_int($width) && $width > 0 && $imageSizes['sq']['w'] != $width) {
                // Width and height are equal for square image
                $imageSizes['sq'] = ['w' => $width, 'h' => $width];

                // Re-create the thumbnails for this size
                if (isset($files)) {
                    foreach ($files as $filename) {
                        $ext = explode('.', $filename)[1];
                        if (strtolower($ext) == 'jpg' || strtolower($ext) == 'png') {
                            // Get original image
                            $upload = new ImageUpload([
                                'type' => 'image/' . strtolower($ext),
                                'new_path' => $imagesDirectory .'/'. $filename
                            ]);
                            $upload->createThumbnail($imagesDirectory .'/sq', null, $width, $width);
                        }
                    }
                }
            }

            // Process other thumbnail sizes
            foreach (['xl', 'l', 'm', 's', 'xs'] as $imageSize) {
                $width = $this->request->getInt($imageSize .'_image_width');
                $height = $this->request->getInt($imageSize .'_image_height');

                $validWidth = is_int($width) && $width > 0;
                $validHeight = is_int($height) && $height > 0;

                // Have the values changed?
                if ($validWidth && $validHeight && ($imageSizes[$imageSize]['w'] != $width || $imageSizes[$imageSize]['h'] != $height)) {
                    $imageSizes[$imageSize] = ['w' => $width, 'h' => $height];

                    // Re-create the thumbnails for this size
                    if (isset($files)) {
                        foreach ($files as $filename) {
                            $ext = explode('.', $filename)[1];
                            if (strtolower($ext) == 'jpg' || strtolower($ext) == 'png') {
                                // Get original image
                                $upload = new ImageUpload([
                                    'type' => 'image/' . strtolower($ext),
                                    'new_path' => $imagesDirectory .'/'. $filename
                                ]);
                                $upload->createThumbnail($imagesDirectory .'/'. $imageSize, null, $width, $height);
                            }
                        }
                    }
                }
            }

            $config = ['files' => ['imagestyles' => $imageSizes]];
            $this->blog->updateConfig($config);
            $this->response->redirect('/cms/files/settings/'. $this->blog->id, 'File settings updated', 'success');
        }

        $this->response->setBreadcrumbs([
            $this->blog->name => $this->blog->url(),
            'Settings' => "/cms/settings/menu/{$this->blog->id}",
            'Files' => null
        ]);
        $this->response->headerIcon = 'sliders horizontal';
        $this->response->headerText = $this->blog->name . ': File settings';

        $this->response->setVar('blog', $this->blog);
        $this->response->setVar('config', $fileConfig);
        $this->response->setTitle('Manage file settings');
        $this->response->write('settings.tpl', 'Files');
    }

    public static function getDefaultImageSizes()
    {
        return [
            'xl' => ['w' => 1600, 'h' => 1200],
            'l' =>  ['w' => 800,  'h' => 600],
            'm' =>  ['w' => 640 , 'h' => 480],
            's' =>  ['w' => 320,  'h' => 240],
            'xs' => ['w' => 100,  'h' =>  75],
            'sq' => ['w' => 180,  'h' => 180],
        ];
    }
}