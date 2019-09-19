<?php
namespace rbwebdesigns\HamletCMS;

use rbwebdesigns\core\Response;
use rbwebdesigns\core\JSONHelper;

class HamletCMSResponse extends Response
{
    protected $smarty;

    public function __construct()
    {
        $this->smarty = new \Smarty;

        $cacheDirectories = [
            'main' => SERVER_ROOT.'/app/view/smarty/'
        ];

        $cacheDir = HamletCMS::getCacheDirectory();
        if (!is_file($cacheDir. '/templates.json')) {
            HamletCMS::generateTemplateCache();
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
        $session = HamletCMS::session();
        $currentUser = $session->currentUser;
        
        // Don't validate if the full file path has been passed in - this
        // will be the case with blog templates as don't want to regsiter
        // a template directory for each blog.
        if(substr($templatePath, 0, 5) === "file:") {
            if ($output) {
                $this->smarty->display($templatePath);
            }
            else {
                return $this->smarty->fetch($templatePath);
            }
        }
        elseif(!file_exists($this->smarty->getTemplateDir($source) . $templatePath)) {
            $debug = print_r(debug_backtrace(), true);
            die('Unable to find template: ' . $templatePath . '<pre>' . $debug . '</pre>'); // todo - create a proper debug class
        }
        elseif ($output) {
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
        $session = HamletCMS::session();

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
     * 
     * @param string $name
     * @param mixed $value
     */
    public function setVar($name, $value)
    {
        $this->smarty->assign($name, $value);
    }

    /**
     * Get the value of a variable in the smarty template
     * 
     * @param string $name
     *   Variable name
     * 
     * @return mixed
     */
    public function getVar($name) {
        return $this->smarty->getTemplateVars($name);
    }

    /**
     * A template may be exposed to a user to edit, give the option
     * to restrict smarty functionality for security purposes.
     * 
     * @see https://www.smarty.net/docsv2/en/variable.security.tpl
     */
    public function enableSecureMode()
    {
        $this->smarty->security = true;
    }
    
}