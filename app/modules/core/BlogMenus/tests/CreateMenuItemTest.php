<?php
namespace HamletCMS\BlogMenus\tests;

use HamletCMS\HamletCMS;
use HamletCMS\tests\TestResult;

/**
 * Creates a new blog menu and items
 */
class CreateMenuItemTest extends TestResult
{

    protected $menu;

    public function __construct($menu) {
        parent::__construct();

        $this->menu = $menu;
    }

    public function run()
    {
        // Instantiate the menu controller
        $controller = new \HamletCMS\BlogMenus\controller\Menus();

        $this->log("Running test CreateMenuItem");

        // Configure request
        $this->request->method = 'POST';
        $this->request->setVariable('type', 'external');
        $this->request->setVariable('text', 'Google');
        $this->request->setVariable('target', 'https://www.google.com/');
        $this->request->setVariable('new_window', 'on');
        $this->request->setUrlParameter(2, $this->menu->id);

        // Create the menu
        $controller->createLink();

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
