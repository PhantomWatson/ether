<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Network\Session;

class FlashComponent extends Component {
	public function beforeRender(\Cake\Event\Event $event) {
		$this->__prepareFlashMessages();
	}

	/**
	 * Adds a flash message / variable dump
	 * @param string $message Message (or variable to be dumped)
	 * @param string $class success, error, notification (default), or dump
	 */
	public function set($message, $class = 'notification') {
		$stored_messages = $this->request->session()->read('FlashMessage');
		$stored_messages[] = compact('message', 'class');
		$this->request->session()->write('FlashMessage', $stored_messages);
	}

	/**
	 * Adds a flash message with 'success' class
	 * @param string $message
	 */
	public function success($message) {
		$this->set($message, 'success');
	}

	/**
	 * Adds a flash message with 'error' class
	 * @param string $message
	 */
	public function error($message) {
		$this->set($message, 'error');
	}

	/**
	 * Adds a flash message with 'notification' class
	 * @param string $message
	 */
	public function notification($message) {
		$this->set($message, 'notification');
	}

	/**
	 * Adds a variable to be output in a flash message
	 * @param string $variable
	 */
	public function dump($variable) {
		$this->set($variable, 'dump');
	}

	/**
	 * Sets an array to be displayed by the element 'flash_messages'
	 * @return array
	 */
	private function __prepareFlashMessages() {
		$stored_messages = $this->request->session()->read('FlashMessage');
		$stored_messages = $stored_messages ? $stored_messages : [];
		$this->request->session()->delete('FlashMessage');

		// Add auth error messages
		if ($auth_error = $this->request->session()->read('Message.auth')) {
			$stored_messages[] = [
				'message' => $auth_error['message'],
				'class' => 'error'
			];
			$this->request->session()->delete('Message.auth');
		}

		// Process variable dumping
		foreach ($stored_messages as &$message) {
			if ($message['class'] == 'dump') {
				$message = [
					'message' => '<pre>'.print_r($message['message'], true).'</pre>',
					'class' => 'notification'
				];
			}
		}

		$this->_registry->getController()->set('flash_messages', $stored_messages);
	}
}