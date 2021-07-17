<?php
namespace HamletCMS\BlogMenus\tests;

use HamletCMS\HamletCMS;
use HamletCMS\tests\TestResult;

/**
 * Creates a new blog menu and items
 */
class CreateMenuTest extends TestResult
{

    public function run()
    {
        // Instantiate the menu controller
        $controller = new \HamletCMS\BlogMenus\controller\Menus();

        $this->log("Running test CreateMenu");

        // Set POST variables
        $this->request->method = 'POST';
        $this->request->setVariable('menu_name', 'Main menu');
        
        // Create the menu
        $controller->createMenu();

        // Check response
        if ($redirect = $this->response->redirect) {
            switch (strtolower($redirect['messageType'])) {
                case 'error':
                    print "Error: Test errored with message - ". $redirect['message'];
                    exit;
                case 'success':
                    $this->log("Test passed");
                    break;
            }
        }
    }

}
