<?php
namespace App\Controller;

use App\Model\Table\CommentsTable;
use App\Model\Table\ThoughtsTable;
use Cake\Datasource\Exception\RecordNotFoundException;
use Cake\Http\Response;
use Cake\ORM\TableRegistry;
use Exception;

/**
 * Comments Controller
 *
 * @property CommentsTable $Comments
 */
class CommentsController extends AppController
{
    /**
     * Initialize method
     *
     * @return void
     * @throws Exception
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Auth->allow(['refreshFormatting']);
    }

    /**
     * Add method
     *
     * @return Response|null
     */
    public function add()
    {
        $comment = $this->Comments->newEmptyEntity();
        if ($this->request->is('post')) {
            $comment = $this->Comments->patchEntity($comment, $this->request->getData());
            $comment->user_id = $this->Auth->user('id');
            if ($this->Comments->save($comment)) {
                $this->Flash->success('Comment posted.');
                $word = $this->Comments->Thoughts->get($this->request->getData('thought_id'))->word;

                return $this->redirect(['controller' => 'Thoughts', 'action' => 'word', $word]);
            } else {
                $this->Flash->error('Your comment could not be posted. Please try again.');

                return $this->redirect($this->request->referer());
            }
        }

        return null;
    }

    /**
     * Refreshes $comment->formatted for a specified comment
     *
     * @param int $commentId Comment ID
     * @return Response|null
     */
    public function refreshFormatting($commentId)
    {
        $this->viewBuilder()
            ->setLayout('json')
            ->setTemplate('/Thoughts/refresh_formatting');

        /** @var ThoughtsTable $thoughtsTable */
        $thoughtsTable = TableRegistry::getTableLocator()->get('Thoughts');

        try {
            $comment = $this->Comments->get($commentId);
        } catch (RecordNotFoundException $e) {
            $this->set([
                'result' => [
                    'success' => false,
                    'update' => false
                ]
            ]);

            return null;
        }

        $formattingKey = $thoughtsTable->getPopulatedThoughtwordHash();
        if ($formattingKey == $comment->formatting_key) {
            $this->set([
                'result' => [
                    'success' => true,
                    'update' => false
                ]
            ]);

            return null;
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
    }
}
