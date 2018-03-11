<?php
namespace rbwebdesigns\blogcms;

use rbwebdesigns\core\Response;

class BlogCMSResponse extends Response
{
    protected $smarty;

    public function __construct()
    {
        $this->smarty = new \Smarty;
        $this->smarty->setTemplateDir([
            'main' => SERVER_ROOT.'/app/view/smarty/'
        ]);

        // Add default stylesheet(s)
        $this->addStylesheet('/css/semantic');
        // $this->addStylesheet('/resources/css/core');
        $this->addStylesheet('/resources/css/header');
        // $this->addStylesheet('/resources/css/forms');
        $this->addStylesheet('/css/blogs_stylesheet');
        
        // Add default script(s)
        $this->addScript('/resources/js/jquery-1.8.0.min');
        $this->addScript('/js/semantic');
        $this->addScript('/resources/js/core-functions');
        $this->addScript('/resources/js/validate');
        $this->addScript('/resources/js/ajax');
        $this->addScript('/js/sidemenu');
    }

    /**
     * Provide a render function that uses smarty template
     */
    public function write($templatePath)
    {
        $session = BlogCMS::session();
        $currentUser = $session->currentUser;

        $this->setVar('messages', $session->getAllMessages());
        
        // Note using this class we are ALWAYS using smarty template
        // $usesmarty = (substr($templatePath, -3) == 'tpl');

        if(!file_exists($this->smarty->getTemplateDir('main') . $templatePath)) {
            $debug = print_r(debug_backtrace(), true);
            die('Unable to find template: ' . $templatePath . '<pre>' . $debug . '</pre>'); // todo - create a proper debug class
        }
        else {
            $this->smarty->display($templatePath);
        }
    }

    /**
     * Overwrites default to use smarty templates
     */
    public function setVar($name, $value)
    {
        $this->smarty->assign($name, $value);
    }

}