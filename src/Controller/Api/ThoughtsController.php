<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use App\Model\Entity\Thought;
use App\TTS;
use Cake\Core\Configure;
use Cake\Http\Response;
use Cake\ORM\TableRegistry;
use Cake\View\JsonView;
use Exception;

/**
 * Thoughts API Controller
 */
class ThoughtsController extends AppController
{
    public function viewClasses(): array
    {
        return [JsonView::class];
    }

    /**
     * Initialize method
     *
     * @return void
     * @throws Exception
     */
    public function initialize(): void
    {
        parent::initialize();
        $this->Auth->allow([
            'tts',
        ]);
        $this->loadComponent('RequestHandler');
    }

    /**
     * isAuthorized() method
     *
     * @param array|null $user User array
     * @return bool
     */
    public function isAuthorized($user = null): bool
    {
        return true;
    }

    /**
     * Endpoint for generating a text-to-speech file (or finding an existing one) and returning the filename
     *
     * @throws \Google\ApiCore\ApiException
     * @throws \Google\ApiCore\ValidationException
     * @return Response|null
     */
    public function tts($thoughtId)
    {
        $isMaintenanceMode = Configure::read('maintenanceMode');
        if ($isMaintenanceMode) {
            return $this->response->withStatus(503);
        }

        $thoughtsTable = TableRegistry::getTableLocator()->get('Thoughts');
        /** @var Thought $thought */
        $thought = $thoughtsTable->get($thoughtId);

        // Audio file exists
        if ($this->ttsFileExists($thought)) {
            $filename = $thought->tts;

        // Generate audio file
        } else {
            // Filename is in DB but file is missing; clear DB field
            if ($thought->tts) {
                $thoughtsTable->patchEntity($thought, ['tts' => null]);
                $thoughtsTable->save($thought);
            }

            $filename = (new TTS())->generate($thought->thought, (string)$thoughtId);
            $thought = $thoughtsTable->patchEntity($thought, ['tts' => $filename]);
            $thoughtsTable->save($thought);
        }

        $this->set(compact('filename'));
        $this->viewBuilder()
            ->setOption('serialize', ['filename'])
            ->setClassName('Json');

        return null;
    }

    /**
     * @param \App\Model\Entity\Thought $thought
     * @return bool
     */
    private function ttsFileExists(Thought $thought): bool
    {
        return $thought->tts && file_exists(TTS::PATH . $thought->tts);
    }
}
