<?php
namespace App\Controller;

use App\Controller\Component\FlashComponent;
use App\Model\Entity\User;
use App\Model\Table\MessagesTable;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\Http\Cookie\Cookie;
use Cake\ORM\TableRegistry;
use Exception;

/**
 * @property FlashComponent $Flash
 */
class AppController extends Controller
{
    public $helpers = [
        'Time' => [
            'className' => 'EtherTime'
        ],
        'Form' => [
            'templates' => 'ether_form'
        ]
    ];

    /**
     * Initialize function
     *
     * @throws Exception
     * @return void
     */
    public function initialize(): void
    {
        $this->loadComponent('RequestHandler');
        $this->loadComponent('Flash');
        $this->loadComponent('Auth', [
            'loginAction' => [
                'controller' => 'Users',
                'action' => 'login'
            ],
            'logoutRedirect' => [
                'controller' => 'Pages',
                'action' => 'home'
            ],
            'authenticate' => [
                'Form' => [
                    'fields' => ['username' => 'email'],
                    'passwordHasher' => [
                        'className' => 'Fallback',
                        'hashers' => ['Default', 'Legacy']
                    ]
                ],
                'Cookie' => [
                    'fields' => [
                        'username' => 'email',
                        'password' => 'password',
                    ],
                ],
            ],
            'authError' => 'You are not authorized to view this page',
            'authorize' => ['Controller']
        ]);
        $this->set('debug', Configure::read('debug'));
    }

    /**
     * Default isAuthorized method, which always returns TRUE, indicating that any logged-in user is authorized
     *
     * @param User|null $user User entity
     * @return bool
     */
    public function isAuthorized($user = null)
    {
        return true;
    }

    /**
     * beforeFilter callback
     *
     * @param Event $event Event object
     * @return void
     */
    public function beforeFilter(\Cake\Event\EventInterface $event)
    {
        $authError = $this->Auth->user('id')
            ? 'Sorry, you do not have access to that location.'
            : 'Please <a href="/login">log in</a> before you try that.';
        $this->Auth->setConfig('authError', $authError);

        // Automatically login
        if (!$this->Auth->user() && $this->getRequest()->getCookie('CookieAuth')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
            } else {
                $this->setResponse($this->getResponse()->withExpiredCookie(new Cookie('CookieAuth')));
            }
        }

        // Replace "You are not authorized" error message with login prompt message if user is not logged in
        if (!$this->Auth->user()) {
            $this->Auth->setConfig('authError', 'You\'ll need to log in before accessing that page');
        }
    }

    /**
     * beforeRender callback
     *
     * @param Event $event Event object
     * @return void
     */
    public function beforeRender(\Cake\Event\EventInterface $event)
    {
        $userId = $this->Auth->user('id');
        /** @var MessagesTable $messagesTable */
        $messagesTable = TableRegistry::getTableLocator()->get('Messages');
        $this->set([
            'userId' => $userId,
            'userColor' => $this->Auth->user('color'),
            'loggedIn' => $userId !== null,
            'newMessages' => $userId ? $messagesTable->getNewMessagesCount($userId) : 0
        ]);
    }
}
