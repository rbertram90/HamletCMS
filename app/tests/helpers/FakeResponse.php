<?php
namespace rbwebdesigns\blogcms\tests;

use rbwebdesigns\blogcms\BlogCMSResponse;

class FakeResponse extends BlogCMSResponse
{
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