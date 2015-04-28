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
 * @link	  http://cakephp.org CakePHP(tm) Project
 * @since	 0.2.9
 * @license   http://www.opensource.org/licenses/mit-license.php MIT License
 */
namespace App\Controller;

use Cake\Controller\Controller;
use Cake\Core\Configure;

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

	public function initialize() {
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
				]
			],
			'authorize' => ['Controller']
		]);
		$this->loadComponent('RememberMe');
		$this->set('debug', Configure::read('debug'));
	}

	public function isAuthorized($user = null)
	{
		return true;
	}

	public function beforeFilter(\Cake\Event\Event $event) {
		$authError = $this->Auth->user('id') ? 'Sorry, you do not have access to that location.' : 'Please <a href="/login">log in</a> before you try that.';
		$this->Auth->config('authError', $authError);
	}

	public function beforeRender(\Cake\Event\Event $event) {
		$userId = $this->Auth->user('id');
		$this->set(array(
			'userId' => $userId,
			'loggedIn' => $userId !== null
		));

		$this->__setNewMessagesAlert();
	}

	/**
	 * Sets the variable $hasNewMessages for logged-in users
	 */
	private function __setNewMessagesAlert() {
		$userId = $this->Auth->user('id');
		if (! $userId) {
			return;
		}

		// Check session
		if ($this->request->session()->check('hasNewMessages')) {
			$hasNewMessages = $this->request->session()->check('hasNewMessages');

		// Check database and save result to session
		} else {
			$this->loadModel('Messages');
			$hasNewMessages = $this->Messages->userHasNewMessages($userId);
			$this->request->session()->write('hasNewMessages', $hasNewMessages);
		}
		$this->set(compact('hasNewMessages'));
	}
}
