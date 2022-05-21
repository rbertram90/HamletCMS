<?php

namespace HamletCMS\Settings\tests;

use HamletCMS\tests\TestResult;
use HamletCMS\Settings\controller\Settings as SettingsController;
use HamletCMS\HamletCMS;

class SettingsTests extends TestResult {

    public $blogID = 0;

    /** @var \HamletCMS\Settings\controller\Settings */
    protected $controller;

    public function run() {
        $this->controller = new SettingsController();
        $this->request->method = 'GET';
        $this->getGeneralSettings();
        $this->getHeaderSettings();
        $this->getFooterSettings();

        $this->request->method = 'POST';
        $this->postGeneralSettings();
        $this->postHeaderSettings();
        $this->postFooterSettings();
    }

    /**
     * View the general settings page.
     * 
     * GET /cms/settings/general/<blog id>
     */
    public function getGeneralSettings() {
        $this->controller->general();
        $output = $this->response->output;

        if (strpos($output, '<input type="submit" class="ui button floated right teal" value="Update" />') !== FALSE) {
            $this->log("Passed SettingsTests::getGeneralSettings");
        }
        else {
            $this->fail("Failed SettingsTests::getGeneralSettings - did find expected string in output.");
        }
    }

    /**
     * Submit the general settings form.
     * 
     * POST /cms/settings/general/<blog id>
     */
    public function postGeneralSettings() {
        /** @var \HamletCMS\Blog\Blog */
        $blog = HamletCMS::model('blogs')->getBlogById($this->blogID);
        $this->request->setVariable('fld_blogname', 'Blog test ' . $blog->id);
        $this->request->setVariable('fld_blogdesc', $blog->description . ' [Updated]');
        $this->request->setVariable('fld_blogsecurity', 'anon');
        $this->request->setVariable('fld_category', 'general');
        $this->request->setVariable('fld_domain', '');
        $this->request->setVariable('fld_homepage_type', 'posts');
        $this->request->setVariable('fld_homepage_post_id', '');
        $this->request->setVariable('fld_tag_sections', '');
        $this->controller->general();

        if ($this->response->redirect['messageType'] === 'success') {
            $this->log('Passed SettingsTests::postGeneralSettings');
        }
        else {
            $this->fail('Failed SettingsTests::postGeneralSettings: ' . $this->response->redirect['message']);
        }
    }

    /**
     * View the header settings page.
     * 
     * GET /cms/settings/header/<blog id>
     */
    public function getHeaderSettings() {
        $this->controller->header();
        $output = $this->response->output;

        if (strpos($output, '<input type="submit" class="ui right floated teal button" value="Submit" name="Save" />') !== FALSE) {
            $this->log("Passed SettingsTests::getHeaderSettings");
        }
        else {
            $this->fail("Failed SettingsTests::getHeaderSettings - did find expected string in output.");
        }
    }

    /**
     * Submit the header settings.
     * 
     * POST /cms/settings/header/<blog id>
     */
    public function postHeaderSettings() {
        $header_template = file_get_contents(SERVER_MODULES_PATH . '/BlogView/templates/header.tpl');
        $this->request->setVariable('fld_headerbackgroundimage', '');
        $this->request->setVariable('fld_horizontalposition', 's');
        $this->request->setVariable('fld_veritcalposition', 's');
        $this->request->setVariable('fld_horizontalalign', 'l');
        $this->request->setVariable('header_template', $header_template);

        $this->controller->header();

        if ($this->response->redirect['messageType'] === 'success') {
            $this->log('Passed SettingsTests::postHeaderSettings');
        }
        else {
            $this->fail('Failed SettingsTests::postHeaderSettings: ' . $this->response->redirect['message']);
        }
    }

    /**
     * View the footer settings page.
     * 
     * GET /cms/settings/footer/<blog id>
     */
    public function getFooterSettings() {
        $this->controller->footer();
        $output = $this->response->output;

        if (strpos($output, '<input type="submit" class="ui button teal right floated" value="Submit" name="Save" />') !== FALSE) {
            $this->log("Passed SettingsTests::getFooterSettings");
        }
        else {
            $this->fail("Failed SettingsTests::getFooterSettings - did find expected string in output.");
        }
    }

    /**
     * Submit the footer settings.
     * 
     * POST /cms/settings/footer/<blog id>
     */
    public function postFooterSettings() {
        $footer_template = file_get_contents(SERVER_MODULES_PATH . '/BlogView/templates/footer.tpl');
        $this->request->setVariable('fld_footerbackgroundimage', '');
        $this->request->setVariable('fld_horizontalposition', 's');
        $this->request->setVariable('fld_veritcalposition', 's');
        $this->request->setVariable('footer_template', $footer_template);

        $this->controller->footer();

        if ($this->response->redirect['messageType'] === 'success') {
            $this->log('Passed SettingsTests::postFooterSettings');
        }
        else {
            $this->fail('Failed SettingsTests::postFooterSettings: ' . $this->response->redirect['message']);
        }
    }

}
