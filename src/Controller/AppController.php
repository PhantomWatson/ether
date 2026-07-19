<?php
declare(strict_types=1);

namespace App\Controller;

use App\Controller\Component\FlashComponent;
use App\Model\Entity\User;
use App\Model\Table\MessagesTable;
use Authentication\Controller\Component\AuthenticationComponent;
use Cake\Controller\Controller;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\ORM\TableRegistry;
use Exception;

/**
 * @property FlashComponent $Flash
 * @property AuthenticationComponent $Authentication
 */
class AppController extends Controller
{
    /**
     * Initialize function
     *
     * @throws Exception
     * @return void
     */
    public function initialize(): void
    {
        $this->loadComponent('Flash');
        $this->loadComponent('Authentication.Authentication', [
            'logoutRedirect' => [
                'controller' => 'Pages',
                'action' => 'home',
            ],
        ]);
        $this->set('debug', Configure::read('debug'));
    }

    /**
     * beforeFilter callback
     *
     * @param EventInterface $event Event object
     * @return Response|null
     */
    public function beforeFilter(EventInterface $event): ?Response
    {
        $isMaintenanceMode = Configure::read('maintenanceMode');
        $alreadyRedirected = $this->getRequest()->getParam('action') == 'maintenanceMode';
        if ($isMaintenanceMode && !$alreadyRedirected) {
            return $this->redirect([
                'controller' => 'Pages',
                'action' => 'maintenanceMode',
            ]);
        }

        return null;
    }

    /**
     * beforeRender callback
     *
     * @param EventInterface $event Event object
     * @return void
     */
    public function beforeRender(EventInterface $event): void
    {
        $identity = $this->Authentication->getIdentity();
        $userId = $identity?->get('id');
        /** @var MessagesTable $messagesTable */
        $messagesTable = TableRegistry::getTableLocator()->get('Messages');
        $this->set([
            'userId' => $userId,
            'userColor' => $identity?->get('color'),
            'loggedIn' => $userId !== null,
            'newMessages' => $userId ? $messagesTable->getNewMessagesCount($userId) : 0
        ]);
    }

    protected function currentUser(): ?User
    {
        return $this->Authentication->getIdentity();
    }
}
