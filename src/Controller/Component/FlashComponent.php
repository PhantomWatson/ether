<?php
namespace App\Controller\Component;

use Cake\Controller\Component;
use Cake\Event\Event;

class FlashComponent extends Component
{
    /**
     * beforeRender method
     *
     * @param Event $event Event object
     * @return void
     */
    public function beforeRender(Event $event)
    {
        $this->__prepareFlashMessages();
    }

    /**
     * Adds a flash message / variable dump
     *
     * @param string $message Message (or variable to be dumped)
     * @param string $class success, error, notification (default), or dump
     * @return void
     */
    public function set($message, $class = 'notification')
    {
        $storedMessages = $this->getController()->request->getSession()->read('FlashMessage');

        // Handle the $options parameter passed by the Authentication component
        if (is_array($class)) {
            if (isset($class['params']['class'])) {
                $class = $class['params']['class'];
            } else {
                $class = 'notification';
            }
        }

        $storedMessages[] = compact('message', 'class');
        $this->getController()->request->getSession()->write('FlashMessage', $storedMessages);
    }

    /**
     * Adds a flash message with 'success' class
     *
     * @param string $message Message
     * @return void
     */
    public function success($message)
    {
        $this->set($message, 'success');
    }

    /**
     * Adds a flash message with 'error' class
     *
     * @param string $message Message
     * @return void
     */
    public function error($message)
    {
        $this->set($message, 'error');
    }

    /**
     * Adds a flash message with 'notification' class
     *
     * @param string $message Message
     * @return void
     */
    public function notification($message)
    {
        $this->set($message, 'notification');
    }

    /**
     * Adds a variable to be output in a flash message
     *
     * @param string $variable Variable to dump
     * @return void
     */
    public function dump($variable)
    {
        $this->set($variable, 'dump');
    }

    /**
     * Sets an array to be displayed by the element 'flashMessages'
     *
     * @return void
     */
    private function __prepareFlashMessages()
    {
        $storedMessages = $this->getController()->request->getSession()->read('FlashMessage');
        $storedMessages = $storedMessages ? $storedMessages : [];
        $this->getController()->request->getSession()->delete('FlashMessage');

        // Add auth error messages
        $authError = $this->getController()->request->getSession()->read('Message.auth');
        if ($authError) {
            $storedMessages[] = [
                'message' => $authError['message'],
                'class' => 'danger'
            ];
            $this->getController()->request->getSession()->delete('Message.auth');
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
