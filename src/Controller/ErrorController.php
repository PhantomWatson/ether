<?php
namespace App\Controller;

use Cake\Event\Event;
use Exception;

class ErrorController extends AppController
{
    /**
     * Initialization hook method.
     *
     * @return void
     * @throws Exception
     */
    public function initialize(): void
    {
        $this->loadComponent('RequestHandler');
        parent::initialize();
    }

    /**
     * beforeRender callback.
     *
     * @param Event $event Event.
     * @return void
     */
    public function beforeRender(\Cake\Event\EventInterface $event)
    {
        parent::beforeRender($event);
        $this->viewBuilder()->setTemplatePath('Error');
    }
}
