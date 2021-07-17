<?php
namespace HamletCMS\tests;

use HamletCMS\HamletCMSResponse;

class FakeResponse extends HamletCMSResponse
{
    /** Array of redirect redetail */
    public $redirect;
    
    /**
     * Overwrite redirects to just return the information provided so we
     * can check expected behaviour
     */
    public function redirect($location, $message = '', $messageType = 'info') {
        $this->redirect = [
            'location' => $location,
            'message' => $message,
            'messageType' => $messageType
        ];
    }

}
