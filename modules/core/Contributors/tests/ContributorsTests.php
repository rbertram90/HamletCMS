<?php

namespace HamletCMS\Contributors\tests;

use HamletCMS\tests\TestResult;
use HamletCMS\Contributors\controller\Contributors as ContributorsController;
use HamletCMS\HamletCMS;

class ContributorsTests extends TestResult
{
    public $blogID = 0;

    /** @var \HamletCMS\Contributors\controller\Contributors */
    protected $controller;

    public function run() {
        $this->controller = new ContributorsController();
        $this->getManageContributors();
        $this->getCreateContributor();

        $this->request->method = 'POST';
        $this->postCreateContributor();
        $this->postInviteContributor();
    }

    /**
     * Backend test: GET /contributors/manage/<blogid>
     */
    public function getManageContributors() {
        // View the manage contributors page
        $this->controller->manage();
        $output = $this->response->output;

        // Look for one of the last things on the page which hopefully means
        // that the rest of the page rendered without error.
        if (strpos($output, '<a href="/cms/contributors/invite/' . $this->blogID . '" class="ui labeled icon teal button"><i class="plus icon"></i> Invite Contributor</a>') !== FALSE) {
            $this->log("Passed ContributorsTests::getManageContributors");
        }
        else {
            $this->fail("Failed ContributorsTests::getManageContributors - did find expected string in output.");
            print $output;
        }
    }

    /**
     * Backend test: GET /contributors/create/<blogid>
     */
    public function getCreateContributor() {
        // View the create contributor page
        $this->controller->create();
        $output = $this->response->output;

        if (strpos($output, '<form action="/cms/contributors/create/' . $this->blogID . '" method="POST" class="ui form">') !== FALSE) {
            $this->log("Passed ContributorsTests::getCreateContributor");
        }
        else {
            print $output;
            $this->fail("Failed ContributorsTests::getCreateContributor - did find expected string in output.");
        }
    }

    /**
     * Backend test: POST /contributors/create/<blogid>
     */
    public function postCreateContributor() {
        $unique_id = uniqid();
        $this->request->setVariable('fld_name', 'John');
        $this->request->setVariable('fld_surname', 'Smith');
        $this->request->setVariable('fld_gender', 'Male');
        $this->request->setVariable('fld_username', 'user_' . $unique_id);
        $this->request->setVariable('fld_password', 'secret');
        $this->request->setVariable('fld_password_2', 'secret');
        $this->request->setVariable('fld_email', 'john' . $unique_id . '@example.com');
        $this->request->setVariable('fld_email_2', 'john' . $unique_id . '@example.com');

        $group = HamletCMS::model('contributorgroups')->get('*', [
            'blog_id' => $this->blogID,
            'super' => 1,
        ], '', '', false);

        $this->request->setVariable('group', $group->id);

        // Disabled these validation checks as currently FakeResponse does not exit after redirecting so function runs through to end.

        // Check that registration fails if passwords do not match
        // $this->request->setVariable('fld_password_2', 'wrong');
        // $this->controller->create();
        // if ($this->response->redirect['message'] !== 'Email or passwords did not match') {
        //     $this->fail('Failed ContributorsTests::postCreateContributor: Password mismatch not // identified, returned - ' . $this->response->redirect['message']);
        // }
        // $this->request->setVariable('fld_password_2', 'secret'); // set correct password

        // Check that registration fails if emails do not match
        // $this->request->setVariable('fld_email_2', 'wrong@example.com');
        // $this->controller->create();
        // if ($this->response->redirect['message'] !== 'Email or passwords did not match') {
        //     $this->fail('Failed ContributorsTests::postCreateContributor: Email mismatch not identified, returned - ' . $this->response->redirect['message']);
        // }
        // $this->request->setVariable('fld_email_2', 'john' . $unique_id . '@example.com'); // set correct email

        // Successful attempt
        $this->controller->create();

        if ($this->response->redirect['messageType'] === 'success') {
            $this->log('Passed ContributorsTests::postCreateContributor');
        }
        else {
            $this->fail('Failed ContributorsTests::postCreateContributor: ' . $this->response->redirect['message']);
        }
    }

    /**
     * Backend test: POST /contributors/invite/<blogid>
     */
    public function postInviteContributor() {
        // Create a new user
        $unique_id = uniqid();
        HamletCMS::model('useraccounts')->register([
            'firstname' => 'Jane',
            'surname' => 'Doe',
            'username' => $unique_id,
            'password' => 'secret',
            'email' => $unique_id . '@example.com',
            'admin' => 0
        ]);

        // Fetch that user
        $newuser = HamletCMS::model('useraccounts')->get('*', [
            'email' => $unique_id . '@example.com'
        ], '', '', false);

        // Create a group with minimal details
        HamletCMS::model('contributorgroups')->insert([
            'blog_id' => $this->blogID,
            'name' => $unique_id,
        ]);

        // Fetch new group
        $newgroup = HamletCMS::model('contributorgroups')->get('*', [
            'blog_id' => $this->blogID,
            'name' => $unique_id,
        ], '', '', false);

        $this->request->setVariable('selected_user', $newuser->id);
        $this->request->setVariable('group', $newgroup->id);

        $this->controller->invite();

        if ($this->response->redirect['messageType'] === 'success') {
            $this->log('Passed ContributorsTests::postInviteContributor');
        }
        else {
            $this->fail('Failed ContributorsTests::postInviteContributor: ' . $this->response->redirect['message']);
        }
    }

}
