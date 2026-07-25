<?php
namespace HamletCMS\tests;

/**
 * Thrown by FakeResponse when a controller triggers a redirect.
 */
class RedirectException extends \Exception
{
    /** @var array  the captured redirect details */
    public $redirect;

    public function __construct(array $redirect)
    {
        $this->redirect = $redirect;
        parent::__construct($redirect['message'] ?? 'redirect');
    }
}
