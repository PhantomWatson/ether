<?php
namespace App\Controller;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link http://book.cakephp.org/3.0/en/controllers/pages-controller.html
 * @property \App\Model\Table\ThoughtsTable $Thoughts
 * @property \App\Model\Table\CommentsTable $Comments
 * @property \App\Model\Table\MessagesTable $Messages
 * @property \App\Model\Table\UsersTable $Users
 */
class OverhaulController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow();
    }

    public function stripSlashes()
    {
        $this->loadModel('Thoughts');
        $this->loadModel('Comments');
        $this->loadModel('Messages');
        $this->loadModel('Users');
        $this->Thoughts->overhaulStripSlashes();
        $this->Comments->overhaulStripSlashes();
        $this->Messages->overhaulStripSlashes();
        $this->Users->overhaulStripSlashes();
        $this->render('/Pages/blank');
    }

    public function convertToMarkdown()
    {
        $this->loadModel('Thoughts');
        $this->loadModel('Comments');
        $this->loadModel('Messages');
        $this->loadModel('Users');
        $this->Thoughts->overhaulToMarkdown();
        $this->Comments->overhaulToMarkdown();
        $this->Messages->overhaulToMarkdown();
        $this->Users->overhaulToMarkdown();
        $this->render('/Pages/blank');
    }
}
