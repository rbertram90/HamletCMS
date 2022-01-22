<?php

namespace HamletCMS\PostComments\tests;

use HamletCMS\HamletCMS;
use HamletCMS\PostComments\Comment;
use HamletCMS\PostComments\controller\Comments as CommentsController;
use HamletCMS\tests\TestResult;

/**
 * Inherited variables:
 *   protected $request
 *   protected $response
 */
class CommentsTest extends TestResult
{
    public $blogID = 0;
    public $postID = 0;

    public function run() {
        $controller = new CommentsController();
        $this->createCommentTest($controller);
        $this->approveCommentTest($controller);
    }

    protected function createCommentTest(CommentsController $commentsController) {
        $this->log("Running CommentsTest::Create");

        $this->request->setVariable('fld_postid', $this->postID);
        $this->request->setVariable('fld_comment', 'This is my lovely comment!');
        $commentsController->add();

        $redirect = $this->response->redirect;

        if ($redirect['messageType'] !== 'success') {
            $this->fail("Error: Test failed - " . $redirect['message']);
        }

        $this->log("Passed CommentsTest::Create");
    }

    protected function approveCommentTest(CommentsController $commentsController) {
        $this->log("Running CommentsTest::Approve");

        // Create a new post
        $this->request->setVariable('fld_postid', $this->postID);
        $commentText = 'This comment should be approved. ' . uniqid();
        $this->request->setVariable('fld_comment', $commentText);
        $commentsController->add();
        // Now approve
        /** @var \HamletCMS\PostComments\model\Comments */
        $commentsModel = HamletCMS::model('comments');
        $comment = $commentsModel->get('*', ['message' => $commentText], '', 1, false);
        
        if (!$comment instanceof Comment) {
            $this->fail("Could not load created comment.");
        }

        $this->request->setUrlParameter(1, $comment->id);
        $commentsController->approve();

        $redirect = $this->response->redirect;

        if ($redirect['messageType'] !== 'success') {
            $this->fail($redirect['message']);
        }

        $this->log("Passed CommentsTest::Approve");
    }

}
