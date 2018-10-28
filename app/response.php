<?php
namespace rbwebdesigns\blogcms;

use rbwebdesigns\core\Response;
use rbwebdesigns\core\JSONHelper;

class BlogCMSResponse extends Response
{
    protected $smarty;

    public function __construct()
    {
        $this->smarty = new \Smarty;

        $cacheDirectories = [
            'main' => SERVER_ROOT.'/app/view/smarty/'
        ];

        $cacheDir = BlogCMS::getCacheDirectory();
        if (!is_file($cacheDir. '/templates.json')) {
            BlogCMS::generateTemplateCache();
        }
        $templateCache = JSONHelper::jsonFileToArray($cacheDir. '/templates.json');
        foreach ($templateCache as $key => $dir) {
            $cacheDirectories[$key] = $dir;
        }

        $this->smarty->setTemplateDir($cacheDirectories);

        $this->smarty->setCacheDir($cacheDir); // not sure this is taking effect? need one for each key?
    }

    /**
     * Provide a render function that uses smarty template
     */
    public function write($templatePath, $source='main', $output = true)
    {
        $session = BlogCMS::session();
        $currentUser = $session->currentUser;

        // $this->setVar('messages', $session->getAllMessages());
        
        // Note using this class we are ALWAYS using smarty template
        // $usesmarty = (substr($templatePath, -3) == 'tpl');

        if(!file_exists($this->smarty->getTemplateDir($source) . $templatePath)) {
            $debug = print_r(debug_backtrace(), true);
            die('Unable to find template: ' . $templatePath . '<pre>' . $debug . '</pre>'); // todo - create a proper debug class
        }
        elseif ($output){
            $this->smarty->display("file:[$source]$templatePath");
        }
        else {
            return $this->smarty->fetch("file:[$source]$templatePath");
        }
    }

    /**
     * Output the template SMARTY style
     */
    public function writeTemplate($templatePath, $source='main')
    {
        $session = BlogCMS::session();

        $this->setVar('scripts', $this->prepareScripts());
        $this->setVar('stylesheets', $this->prepareStylesheets());
        $this->setVar('body_content', $this->body);
        $this->setVar('current_user', $session->currentUser);
        $this->setVar('messages', $session->getAllMessages());

        if(!file_exists($this->smarty->getTemplateDir($source) . $templatePath)) {
            $debug = print_r(debug_backtrace(), true);
            die('Unable to find template: ' . $templatePath . '<pre>' . $debug . '</pre>'); // todo - create a proper debug class
        }
        else {
            $this->smarty->display("file:[$source]$templatePath");
        }
    }

    /**
     * Overwrites default to use smarty templates
     */
    public function setVar($name, $value)
    {
        $this->smarty->assign($name, $value);
    }

    public function getVar($name) {
        return $this->smarty->getTemplateVars($name);
    }

}