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

    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
    }

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

    public function send()
    {
        if ($this->request->is('post')) {

            $recipientId = $this->request->data['recipient_id'];
            $senderId = $this->Auth->user('id');
            $this->request->data['sender_id'] = $senderId;
            $this->request->data['message'] = trim($this->request->data['message']);

            if ($this->request->data['message'] == '') {
                $this->Flash->error('You can\'t send a blank message. It would be terribly disappointing for the recipient.');
            } else {
                $recipient = $this->Messages->Recipients->get($recipientId);
                if ($recipient->acceptMessages) {
                    $message = $this->Messages->newEntity();
                    $message = $this->Messages->patchEntity($message, $this->request->data);
                    if ($this->Messages->save($message)) {
                        $this->Flash->success('Message sent.');
                        $recipient->newMessages = true;
                        $this->Messages->Recipients->save($recipient);
                    } else {
                        $this->Flash->error('There was an error sending that message. Please try again.');
                        $this->Flash->dump($message->errors());
                    }
                } else {
                    $this->Flash->error('Sorry, this Thinker has chosen not to receive messages. Your message was not sent. :(');
                }
            }

            if (! $this->request->is('ajax')) {
                $this->redirect($this->request->referer());
            }
        }
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

    public function conversation($penpalColor = null)
    {
        $userId = $this->Auth->user('id');
        $this->loadModel('Users');
        $penpalId = $this->Users->getIdFromColor($penpalColor);
        $this->set(array(
            'messages' => $this->Messages->getConversation($userId, $penpalId),
            'penpalId' => $penpalId,
            'penpalAcceptsMessages' => $this->Users->acceptsMessages($penpalId),
            'messageEntity' => $this->Messages->newEntity()
        ));
    }
}
