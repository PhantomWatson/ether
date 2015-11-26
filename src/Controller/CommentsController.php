<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\ORM\TableRegistry;

/**
 * Comments Controller
 *
 * @property \App\Model\Table\CommentsTable $Comments
 */
class CommentsController extends AppController
{

    public function initialize()
    {
        parent::initialize();
        $this->Auth->allow(['refreshFormatting']);
    }

    /**
     * Add method
     *
     * @return void
     */
    public function add()
    {
        $comment = $this->Comments->newEntity();
        if ($this->request->is('post')) {
            $comment = $this->Comments->patchEntity($comment, $this->request->data);
            $comment->user_id = $this->Auth->user('id');
            if ($this->Comments->save($comment)) {
                $this->Flash->success('Comment posted.');
                $word = $this->Comments->Thoughts->get($this->request->data['thought_id'])->word;
                return $this->redirect(['controller' => 'Thoughts', 'action' => 'word', $word]);
            } else {
                $this->Flash->error('Your comment could not be posted. Please try again.');
                return  $this->redirect($this->request->referer());
            }
        }
    }

    public function refreshFormatting($commentId)
    {
        $this->layout = 'json';
        $thoughtsTable = TableRegistry::get('Thoughts');

        try {
            $comment = $this->Comments->get($commentId);
        } catch (RecordNotFoundException $e) {
            $this->set([
                'result' => [
                    'success' => false,
                    'update' => false
                ]
            ]);
            return;
        }

        $formattingKey = $thoughtsTable->getPopulatedThoughtwordHash();
        if ($formattingKey == $comment->formatting_key) {
            $this->set([
                'result' => [
                    'success' => true,
                    'update' => false
                ]
            ]);
            return;
        }

        $formattedComment = $thoughtsTable->formatThought($comment->comment);
        $comment->formatted_comment = $formattedComment;
        $this->Comments->save($comment);

        $this->set([
            'result' => [
                'success' => true,
                'update' => true,
                'formattedThought' => $formattedComment
            ]
        ]);
        return $this->render('/Thoughts/refresh_formatting');
    }
}
