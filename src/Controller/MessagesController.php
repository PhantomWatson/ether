<?php
namespace App\Controller;

use App\Model\Table\MessagesTable;
use App\Model\Table\UsersTable;
use Cake\Http\Response;
use Cake\Utility\Hash;
use Exception;

/**
 * Messages Controller
 *
 * @property MessagesTable $Messages
 * @property UsersTable $Users
 */
class MessagesController extends AppController
{
    public $paginate = [
        'limit' => 10,
        'order' => [
            'Messages.created' => 'DESC'
        ]
    ];

    /**
     * Initialize method
     *
     * @return void
     * @throws Exception
     */
    public function initialize()
    {
        parent::initialize();
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Paginator');
    }

    /**
     * Renders /messages
     *
     * @param string|null $penpalColor Color of other user
     * @return void
     */
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
     *
     * @return Response|null
     */
    public function send()
    {
        if ($this->request->is('post')) {
            // Gather data
            $this->loadModel('Users');
            $recipientId = $this->Users->getIdFromColor($this->request->getData('recipient'));
            $data = $this->request->getData();
            $data['recipient_id'] = $recipientId;
            $data['sender_id'] = $this->Auth->user('id');
            $data['message'] = trim($data['message']);

            $errorMsg = null;
            $msgSent = false;
            $message = $this->Messages->newEntity();
            $message = $this->Messages->patchEntity($message, $data);
            if ($message->getErrors()) {
                $errors = Hash::flatten($message->getErrors());
                $errorMsg = implode('<br />', $errors);
            } elseif ($this->Messages->save($message)) {
                $msgSent = true;
                $senderId = $this->Auth->user('id');
                if ($this->Users->acceptsMessages($recipientId)) {
                    $this->Messages->sendNotificationEmail($senderId, $recipientId, $message);
                }
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

        return null;
    }

    /**
     * Renders /messages/conversation
     *
     * @param string|null $penpalColor Color of other user
     * @return Response|null
     */
    public function conversation($penpalColor = null)
    {
        $userId = $this->Auth->user('id');
        $this->loadModel('Users');
        $penpalId = $this->Users->getIdFromColor($penpalColor);
        $penpal = $this->Users->get($penpalId);
        $query = $this->Messages->getConversation($userId, $penpalId);
        if (isset($_GET['full'])) {
            $messages = $query->toArray();
        } else {
            $messages = $this->paginate($query)->toArray();
            $messages = array_reverse($messages);
        }
        $this->Messages->setConversationAsRead($userId, $penpalId);

        $this->set([
            'messages' => $messages,
            'penpalId' => $penpalId
        ]);

        // Just render the content, not the whole template
        if ($this->request->is('ajax') || $this->getRequest()->getQuery('page')) {
            // This isn't JSON, but the 'json' template returns JUST the content and nothing else, like we need
            $this->viewBuilder()->setLayout('json');

            return $this->render(DS . 'Element' . DS . 'Messages' . DS . 'conversation');
        }

        $this->set([
            'titleForLayout' => 'Messages with Thinker #' . $penpalColor,
            'penpalColor' => $penpal->color,
            'penpalAcceptsMessages' => $this->Users->acceptsMessages($penpalId),
            'messageEntity' => $this->Messages->newEntity()
        ]);

        return null;
    }
}
