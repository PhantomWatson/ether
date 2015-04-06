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

use Cake\Controller\Controller;

// For initialize()
use Cake\Core\Configure;

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @link http://book.cakephp.org/3.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller
{

    /**
     * Initialization hook method.
     *
     * Use this method to add common initialization code like loading components.
     *
     * @return void
     */
    public function initialize() {
        $this->loadComponent('Flash');

		$this->loadComponent('Auth', [
			'loginAction' => [
				'controller' => 'Users',
				'action' => 'login'
			],
			'authenticate' => [
				'Form' => [
					'fields' => ['username' => 'email']
				]
			]
		]);

		$this->set('debug', Configure::read('debug'));
    }

	public function beforeRender(\Cake\Event\Event $event) {
		$this->Auth->authError = ($this->Auth->user('id')) ?
			'Sorry, you do not have access to that location.' :
			'Please <a href="/login">log in</a> before you try that.';
		$this->set(array(
			'logged_in' => $this->Auth->user('id') != null
		));
	}
}
