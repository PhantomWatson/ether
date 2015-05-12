<?php
namespace App\Controller;

use App\Controller\AppController;

/**
 * Messages Controller
 *
 * @property \App\Model\Table\MessagesTable $Messages
 */
class MessagesController extends AppController
{

    public function index($penpalUserId = null)
    {
        $userId = $this->Auth->user('id');
        $this->loadModel('Users');
        $this->Users->setMessagesUnread($userId);
        $this->set(array(
            'title_for_layout' => 'Messages',
            'conversations' => $this->Messages->getConversationsIndex($userId),
            'selected_user_id' => $penpalUserId
        ));
    }

    /**
     * View method
     *
     * @param string|null $id Message id
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function view($id = null)
    {
        $message = $this->Messages->get($id, [
            'contain' => ['Recipients', 'Senders']
        ]);
        $this->set('message', $message);
    }

    /**
     * Add method
     *
     * @return void
     */
    public function add()
    {
        $message = $this->Messages->newEntity();
        if ($this->request->is('post')) {
            $message = $this->Messages->patchEntity($message, $this->request->data);
            if ($this->Messages->save($message)) {
                $this->Flash->success('The message has been saved.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('The message could not be saved. Please, try again.');
            }
        }
        $recipients = $this->Messages->Recipients->find('list', ['limit' => 200]);
        $senders = $this->Messages->Senders->find('list', ['limit' => 200]);
        $this->set(compact('message', 'recipients', 'senders'));
    }

    /**
     * Edit method
     *
     * @param string|null $id Message id
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function edit($id = null)
    {
        $message = $this->Messages->get($id, [
            'contain' => []
        ]);
        if ($this->request->is(['patch', 'post', 'put'])) {
            $message = $this->Messages->patchEntity($message, $this->request->data);
            if ($this->Messages->save($message)) {
                $this->Flash->success('The message has been saved.');
                return $this->redirect(['action' => 'index']);
            } else {
                $this->Flash->error('The message could not be saved. Please, try again.');
            }
        }
        $recipients = $this->Messages->Recipients->find('list', ['limit' => 200]);
        $senders = $this->Messages->Senders->find('list', ['limit' => 200]);
        $this->set(compact('message', 'recipients', 'senders'));
    }

    /**
     * Delete method
     *
     * @param string|null $id Message id
     * @return void
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function delete($id = null)
    {
        $this->request->allowMethod(['post', 'delete']);
        $message = $this->Messages->get($id);
        if ($this->Messages->delete($message)) {
            $this->Flash->success('The message has been deleted.');
        } else {
            $this->Flash->error('The message could not be deleted. Please, try again.');
        }
        return $this->redirect(['action' => 'index']);
    }
}
