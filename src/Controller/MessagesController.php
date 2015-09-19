<?php
namespace App\Controller;

use App\Controller\AppController;
use Cake\Utility\Hash;

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

    public function index($penpalColor = null)
    {
        $userId = $this->Auth->user('id');
        $this->loadModel('Users');
        $this->set([
            'title_for_layout' => 'Messages',
            'conversations' => $this->Messages->getConversationsIndex($userId),
            'penpalColor' => $penpalColor
        ]);
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
     * The target of message-sending forms. If requested via AJAX, the view
     * will contain a JSON object describing the results. Otherwise, redirects back
     * to the referer with a flash message.
     */
    public function send()
    {
        if ($this->request->is('post')) {
            // Gather data
            $this->loadModel('Users');
            $recipientId = $this->Users->getIdFromColor($this->request->data['recipient']);
            $this->request->data['recipient_id'] = $recipientId;
            $this->request->data['sender_id'] = $this->Auth->user('id');
            $this->request->data['message'] = trim($this->request->data['message']);

            $errorMsg = null;
            $msgSent = false;
            $message = $this->Messages->newEntity();
            $message = $this->Messages->patchEntity($message, $this->request->data);
            if ($message->errors()) {
                $errors = Hash::flatten($message->errors());
                $errorMsg = implode('<br />', $errors);
            } elseif ($this->Messages->save($message)) {
                $msgSent = true;
                $recipient = $this->Messages->Recipients->get($recipientId);
                $recipient->newMessages = true;
                $this->Messages->Recipients->save($recipient);
            } else {
                $errorMsg = 'There was an error sending that message. Please try again.';
            }

            if ($this->request->is('ajax')) {
                $this->set('result', [
                    'error' => $errorMsg,
                    'success' => $msgSent
                ]);
            } else {
                if ($errorMsg) {
                    $this->Flash->error($errorMsg);
                }
                if ($msgSent) {
                    $this->Flash->success('Message sent');
                }
                return $this->redirect($this->request->referer());
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
        $penpal = $this->Users->get($penpalId);
        $this->set([
            'titleForLayout' => 'Messages with Thinker #'.$penpalColor,
            'messages' => $this->Messages->getConversation($userId, $penpalId),
            'penpalId' => $penpalId,
            'penpalColor' => $penpal->color,
            'penpalAcceptsMessages' => $this->Users->acceptsMessages($penpalId),
            'messageEntity' => $this->Messages->newEntity()
        ]);
        $this->Messages->setConversationAsRead($userId, $penpalId);
    }
}
