<?php
namespace App\Controller;

use App\Model\Table\CommentsTable;
use App\Model\Table\MessagesTable;
use App\Model\Table\ThoughtsTable;
use App\Model\Table\UsersTable;
use Exception;

/**
 * Static content controller
 *
 * This controller will render views from Template/Pages/
 *
 * @link http://book.cakephp.org/3.0/en/controllers/pages-controller.html
 * @property ThoughtsTable $Thoughts
 * @property CommentsTable $Comments
 * @property MessagesTable $Messages
 * @property UsersTable $Users
 */
class OverhaulController extends AppController
{
    /**
     * Initialize method
     *
     * @throws Exception
     * @return void
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Auth->allow();
    }

    /**
     * Strips all slashes from database content
     *
     * @return void
     */
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

    /**
     * Converts all database content to markdown
     *
     * @return void
     */
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
