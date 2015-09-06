<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Network\Session;

class FlashComponent extends Component
{
    public function beforeRender(\Cake\Event\Event $event)
    {
        $this->__prepareFlashMessages();
    }

    /**
     * Adds a flash message / variable dump
     * @param string $message Message (or variable to be dumped)
     * @param string $class success, error, notification (default), or dump
     */
    public function set($message, $class = 'notification')
    {
        $storedMessages = $this->request->session()->read('FlashMessage');

        // Handle the $options parameter passed by the Authentication component
        if (is_array($class)) {
            if (isset($class['params']['class'])) {
                $class = $class['params']['class'];
            } else {
                $class = 'notification';
            }
        }

        $storedMessages[] = compact('message', 'class');
        $this->request->session()->write('FlashMessage', $storedMessages);
    }

    /**
     * Adds a flash message with 'success' class
     * @param string $message
     */
    public function success($message)
    {
        $this->set($message, 'success');
    }

    /**
     * Adds a flash message with 'error' class
     * @param string $message
     */
    public function error($message)
    {
        $this->set($message, 'error');
    }

    /**
     * Adds a flash message with 'notification' class
     * @param string $message
     */
    public function notification($message)
    {
        $this->set($message, 'notification');
    }

    /**
     * Adds a variable to be output in a flash message
     * @param string $variable
     */
    public function dump($variable)
    {
        $this->set($variable, 'dump');
    }

    /**
     * Sets an array to be displayed by the element 'flashMessages'
     * @return array
     */
    private function __prepareFlashMessages()
    {
        $storedMessages = $this->request->session()->read('FlashMessage');
        $storedMessages = $storedMessages ? $storedMessages : [];
        $this->request->session()->delete('FlashMessage');

        // Add auth error messages
        $authError = $this->request->session()->read('Message.auth');
        if ($authError) {
            $storedMessages[] = [
                'message' => $authError['message'],
                'class' => 'danger'
            ];
            $this->request->session()->delete('Message.auth');
        }

        // Process variable dumping and convert classes to Bootstrap alert class suffixes
        foreach ($storedMessages as &$message) {
            switch ($message['class']) {
                case 'dump':
                    $message = [
                        'message' => '<pre>'.print_r($message['message'], true).'</pre>',
                        'class' => 'info'
                    ];
                    break;
                case 'notification':
                    $message['class'] = 'info';
                    break;
                case 'error':
                    $message['class'] = 'danger';
                    break;
            }
        }

        $this->_registry->getController()->set('flashMessages', $storedMessages);
    }
}
