<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Comments Controller
 *
 * @property \App\Model\Table\CommentsTable $Comments
 */
class CommentsController extends AppController
{

    /**
     * Index method
     *
     * @return void
     */
    public function index()
    {
        $this->paginate = [
            'contain' => ['Thoughts', 'Users']
        ];
        $this->set('comments', $this->paginate($this->Comments));
    }

    /**
     * View method
     *
     * @param string|null $id Comment id
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function view($id = null)
    {
        $comment = $this->Comments->get($id, [
            'contain' => ['Thoughts', 'Users']
        ]);
        $this->set('comment', $comment);
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

    /**
     * Edit method
     *
     * @param string|null $id Comment id
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function edit($id = null)
    {
        $comment = $this->Comments->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $comment = $this->Comments->patchEntity($comment, $this->request->data);
            if ($this->Comments->save($comment)) {
                $this->Flash->success('The comment has been saved.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('The comment could not be saved. Please, try again.');
            }
        }
        $thoughts = $this->Comments->Thoughts->find('list', ['limit' => 200]);
        $users = $this->Comments->Users->find('list', ['limit' => 200]);
        $this->set(compact('comment', 'thoughts', 'users'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Comment id
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $comment = $this->Comments->get($id);
        if ($this->Comments->delete($comment)) {
            $this->Flash->success('The comment has been deleted.');
        } else {
            $this->Flash->error('The comment could not be deleted. Please, try again.');
        }
        return $this->redirect(['action' => 'index']);
    }
}
