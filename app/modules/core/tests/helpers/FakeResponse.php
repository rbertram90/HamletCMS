<?php
namespace HamletCMS\tests;

use HamletCMS\HamletCMSResponse;
use HamletCMS\HamletCMS;

class FakeResponse extends HamletCMSResponse
{
    /** Array of redirect redetail */
    public $redirect;

    /** Parsed template */
    public $output;
    
    /**
     * Overwrite redirects to just return the information provided so we
     * can check expected behaviour
     */
    public function redirect($location, $message = '', $messageType = 'info')
    {
        $this->redirect = [
            'location' => $location,
            'message' => $message,
            'messageType' => $messageType
        ];
    }

    /**
     * Overwrite routeRedirect to just return information
     */
    public function routeRedirect($route, $message = '', $messageType = '', $data = [])
    {
        $this->redirect = [
            'route' => $route,
            'location' => HamletCMS::route($route, $data),
            'message' => $message,
            'messageType' => $messageType
        ];
    }

    /**
     * Always return the smarty output, rather than writing to output buffer.
     */
    public function write($templatePath, $source = 'main', $output = false)
    {
        $this->output = parent::write($templatePath, $source, false);
    }

}
