<?php
namespace HamletCMS\FavouriteBlogs\tests;

use HamletCMS\HamletCMS;
use HamletCMS\tests\TestResult;

/**
 * Add the current blog to favourites
 */
class FavouriteBlogTest extends TestResult
{
    public $blogID;

    public function run()
    {
        // Instantiate the Favourites controller
        $controller = new \HamletCMS\FavouriteBlogs\controller\Favourites();

        $this->log("Running test CreateFavouriteBlogTest");

        // Set POST variables
        $this->request->method = 'POST';

        // Call the method under test
        $controller->addBlogToFavourites();

        // Check response
        if ($redirect = $this->response->redirect) {
            switch (strtolower($redirect['messageType'])) {
                case 'error':
                    $this->fail("Test errored with message - " . $redirect['message']);
                    exit;
                case 'success':
                    $this->log("CreateFavouriteBlogTest passed");
                    break;
            }
        }
    }

}
