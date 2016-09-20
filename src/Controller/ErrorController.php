<?php
namespace App\Controller;

use Cake\Event\Event;

class ErrorController extends AppController
{
    /**
     * Initialization hook method.
     *
     * @return void
     */
    public function initialize()
    {
        parent::initialize();
    }

    /**
     * beforeRender callback.
     *
     * @param \Cake\Event\Event $event Event.
     * @return void
     */
    public function beforeRender(Event $event)
    {
        $this->viewBuilder()->templatePath('Error');
        parent::beforeRender($event);
    }
}
