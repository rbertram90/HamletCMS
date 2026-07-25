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
     * Record the redirect and halt the action.
     */
    public function redirect($location, $message = '', $messageType = 'info')
    {
        $this->redirect = [
            'location' => $location,
            'message' => $message,
            'messageType' => $messageType
        ];

        throw new RedirectException($this->redirect);
    }

    /**
     * Record a route redirect and halt the action (see redirect()).
     */
    public function routeRedirect($route, $message = '', $messageType = '', $data = [])
    {
        $this->redirect = [
            'route' => $route,
            'location' => HamletCMS::route($route, $data),
            'message' => $message,
            'messageType' => $messageType
        ];

        throw new RedirectException($this->redirect);
    }

    /**
     * Always return the smarty output, rather than writing to output buffer.
     */
    public function write($templatePath, $source = 'main', $output = false)
    {
        $this->output = parent::write($templatePath, $source, false);
    }

}
