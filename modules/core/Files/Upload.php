<?php

namespace HamletCMS\Files;

use HamletCMS\Files\controller\FileSettings;
use rbwebdesigns\core\ImageUpload;

class Upload {

    /**
     * Copy a picture from the web and save as blog file.
     * 
     * @param \HamletCMS\Blog\Blog $blog
     * @param string $file
     *   URL to the image
     * 
     * @return string File name for saving to DB
     */
    public static function fromURL($blog, $file) {
        $urlInfo = parse_url($file);
        $ext = pathinfo($urlInfo['path'])['extension'];

        // Upload
        $newFileName = self::createFileName() . '.' . $ext;
        $path = SERVER_PATH_BLOGS . '/'. $blog->id .'/images/'. $newFileName;
        $save = file_put_contents($path, file_get_contents($file));

        if (!$save) {
            return false;
        }

        // @todo - create thumbnails!
        $imageDirectory = SERVER_PATH_BLOGS . '/' . $blog->id . '/images';

        $blogConfig = $blog->config();
        $sizes = $blogConfig['files']['imagestyles'] ?? FileSettings::getDefaultImageSizes();

        $upload = new ImageUpload(['new_path' => $path, 'type' => 'image/' . $ext]);

        // Create thumbnails
        $upload->createThumbnail($imageDirectory . '/xl', null, $sizes['xl']['w'], $sizes['xl']['h']);
        $upload->createThumbnail($imageDirectory . '/l', null, $sizes['l']['w'], $sizes['l']['h']);
        $upload->createThumbnail($imageDirectory . '/m', null, $sizes['m']['w'], $sizes['m']['h']);
        $upload->createThumbnail($imageDirectory . '/s', null, $sizes['s']['w'], $sizes['s']['h']);
        $upload->createThumbnail($imageDirectory . '/xs', null, $sizes['xs']['w'], $sizes['xs']['h']);
        $upload->createThumbnail($imageDirectory . '/sq', null, $sizes['sq']['w'], $sizes['sq']['h']);

        return $newFileName;
    }

    public static function createFileName() {
        // @todo check if it already exists
        return rand(10000,32000) . rand(10000,32000);
    }

}
