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
    }

    /**
     * Provide a render function that uses smarty template
     */
    public function write($templatePath)
    {
        $session = BlogCMS::session();
        $currentUser = $session->currentUser;

        // $this->setVar('messages', $session->getAllMessages());
        
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
     * Output the template SMARTY style
     */
    public function writeTemplate($templatePath)
    {
        $session = BlogCMS::session();

        $this->setVar('scripts', $this->prepareScripts());
        $this->setVar('stylesheets', $this->prepareStylesheets());
        $this->setVar('body_content', $this->body);
        $this->setVar('current_user', $session->currentUser);
        $this->setVar('messages', $session->getAllMessages());

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