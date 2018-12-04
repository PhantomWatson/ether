<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link      http://cakephp.org CakePHP(tm) Project
 * @since     0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use App\Model\Table\MessagesTable;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\Event;
use Cake\ORM\TableRegistry;

/**
 * @property \App\Controller\Component\FlashComponent $Flash
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
     * @throws \Exception
     */
    public function initialize()
    {
        $this->loadComponent('Cookie');
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
                'Xety/Cake3CookieAuth.Cookie' => [
                    'fields' => ['username' => 'email']
                ]
            ],
            'authorize' => ['Controller']
        ]);
        $this->set('debug', Configure::read('debug'));
    }

    public function isAuthorized($user = null)
    {
        return true;
    }

    public function beforeFilter(Event $event)
    {
        $authError = $this->Auth->user('id') ? 'Sorry, you do not have access to that location.' : 'Please <a href="/login">log in</a> before you try that.';
        $this->Auth->setConfig('authError', $authError);

        // Automaticaly login
        if (! $this->Auth->user() && $this->Cookie->read('CookieAuth')) {
            $user = $this->Auth->identify();
            if ($user) {
                $this->Auth->setUser($user);
            } else {
                $this->Cookie->delete('CookieAuth');
            }
        }
    }

    public function beforeRender(Event $event)
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
