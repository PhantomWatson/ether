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
        if ($thought->tts) {
            $filename = $thought->tts;

        // Generate audio file
        } else {
            $tts = new TTS();
            $text = $thought->thought;

            // Text is under the input limit
            if (mb_strlen($text) < TTS::INPUT_LIMIT) {
                $filename = $tts->generate($text, (string)$thoughtId);

            // Text is over the limit; make multiple audio files and combine them
            } else {
                $partFilePaths = [];
                $partNum = 1;
                $words = explode(' ', $text);
                do {
                    $partText = '';
                    while ($words && mb_strlen($partText . $words[0]) < TTS::INPUT_LIMIT) {
                        $word = array_shift($words);
                        $partText .= $word . ' ';
                    }
                    $partFilePaths[] = TTS::PATH . $tts->generate($partText, "$thoughtId.$partNum");
                    $partNum++;
                } while ($words);

                // Combine files
                $filename = $thoughtId . TTS::EXTENSION;
                $combinedFilepath = TTS::PATH . $filename;
                file_put_contents($combinedFilepath, '');
                (new PhpMp3())->multiJoin($combinedFilepath, $partFilePaths);

                // Cleanup
                foreach ($partFilePaths as $filePath) {
                    unlink($filePath);
                }
            }

            // Save filename to thought
            $thought = $thoughtsTable->patchEntity($thought, ['tts' => $filename]);
            $thoughtsTable->save($thought);
        }

        $this->set(compact('filename'));
        $this->set('_serialize', ['filename']);

        $this->RequestHandler->renderAs($this, 'json');
    }
}
