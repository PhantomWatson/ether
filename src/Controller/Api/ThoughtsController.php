<?php
namespace App\Controller\Api;

use App\Controller\AppController;
use App\Model\Entity\Thought;
use App\PhpMp3;
use App\TTS;
use Cake\ORM\TableRegistry;
use Exception;

/**
 * Thoughts API Controller
 */
class ThoughtsController extends AppController
{
    /**
     * Initialize method
     *
     * @return void
     * @throws Exception
     */
    public function initialize()
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
     */
    public function tts($thoughtId)
    {
        $thoughtsTable = TableRegistry::getTableLocator()->get('Thoughts');
        /** @var Thought $thought */
        $thought = $thoughtsTable->get($thoughtId);

        // Audio file exists
        if ($thought->tts && file_exists(TTS::PATH . $thought->tts)) {
            $filename = $thought->tts;

        // Generate audio file
        } else {
            // Filename is in DB but file is missing; clear DB field
            if ($thought->tts && !file_exists(TTS::PATH . $thought->tts)) {
                $thoughtsTable->patchEntity($thought, ['tts' => null]);
                $thoughtsTable->save($thought);
            }

            $filename = (new TTS())->generate($thought->thought, (string)$thoughtId);
            $thought = $thoughtsTable->patchEntity($thought, ['tts' => $filename]);
            $thoughtsTable->save($thought);
        }

        $this->set(compact('filename'));
        $this->set('_serialize', ['filename']);

        $this->RequestHandler->renderAs($this, 'json');
    }
}
