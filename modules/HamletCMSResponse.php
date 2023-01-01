<?php
namespace HamletCMS;

use rbwebdesigns\core\Response;
use rbwebdesigns\core\JSONHelper;
use Smarty_Security;

class HamletCMSResponse extends Response
{
    protected $smarty;
    protected $breadcrumbs = [];
    public $headerText = '';
    public $headerIcon = '';

    public function __construct()
    {
        $this->smarty = new \Smarty;

        $cacheDirectories = [
            'main' => SERVER_ROOT.'/app/view/smarty/',
            'public' => SERVER_PATH_BLOGS,
        ];

        $cacheDir = HamletCMS::getCacheDirectory();
        if (!is_file($cacheDir. '/templates.json')) {
            HamletCMS::generateSmartyTemplateCache();
        }
        $templateCache = JSONHelper::jsonFileToArray($cacheDir. '/templates.json');
        foreach ($templateCache as $key => $dir) {
            $cacheDirectories[$key] = $dir;
        }

        $this->smarty->setTemplateDir($cacheDirectories);

        $this->smarty->setCacheDir($cacheDir); // not sure this is taking effect? need one for each key?

        $this->smarty->registerPlugin('modifier', 'except', function ($array, $valueToRemove) {
            if (($key = array_search($valueToRemove, $array)) !== FALSE) {
                unset($array[$key]);
            }
            return implode(',', $array);
        });
    }

    /**
     * Provide a render function that uses smarty template
     */
    public function write($templatePath, $source = 'main', $output = true)
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
    public function writeTemplate($templatePath, $source = 'main')
    {
        $session = HamletCMS::session();

        $this->setVar('scripts', $this->prepareScripts());
        $this->setVar('stylesheets', $this->prepareStylesheets());
        $this->setVar('meta', $this->prepareMeta());
        $this->setVar('body_content', $this->body);
        $this->setVar('current_user', $session->currentUser);
        $this->setVar('messages', $session->getAllMessages());
        $this->setVar('breadcrumbs', $this->breadcrumbs);
        $this->setVar('header_text', $this->headerText);
        $this->setVar('header_icon', $this->headerIcon);

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
        $my_security_policy = new Smarty_Security($this->smarty);
        $my_security_policy->php_functions = array_merge($my_security_policy->php_functions, [
            'strlen', 'str_replace'
        ]);

        $this->smarty->enableSecurity($my_security_policy);
    }
    
    /**
     * Redirect, but using a HamletCMS route.
     */
    public function routeRedirect($route, $message = '', $messageType = '', $data = [])
    {
        $path = HamletCMS::route($route, $data);
        if (!$path) {
            $path = '/cms';
            $message = 'Access Denied';
            $messageType = 'error';
        }
        parent::redirect($path, $message, $messageType);
    }

    public function setBreadcrumbs($links) {
        $this->breadcrumbs = ['Home' => '/cms/blog'] + $links;
    }

}
