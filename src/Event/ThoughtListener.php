<?php
namespace App\Event;

use App\Model\Entity\Thought;
use App\Model\Table\ThoughtsTable;
use App\TTS;
use Cake\Event\Event;
use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;

class ThoughtListener implements EventListenerInterface
{

    public function implementedEvents(): array
    {
        return [
            'Model.Thought.created' => [
                ['callable' => 'updatePopulatedThoughtwords']
            ],
            'Model.Thought.updated' => [
                ['callable' => 'thoughtUpdated']
            ],
            'Model.Thought.deleted' => [
                ['callable' => 'thoughtDeleted']
            ]
        ];
    }

    /**
     * Updates the cache of populated thoughtwords
     *
     * @param Event $event Event
     * @param Thought $thought Entity
     */
    public function updatePopulatedThoughtwords($event, $thought)
    {
        // Exit if entity was updated without changing word
        if (!$thought->isNew() && !$thought->isDirty('word')) {
            return;
        }

        // Exit if this is a new thought on an already-populated thoughtword
        /** @var ThoughtsTable $thoughtsTable */
        $thoughtsTable = TableRegistry::getTableLocator()->get('Thoughts');
        if ($thought->isNew() && $thoughtsTable->getPopulation($thought->word) > 1) {
            return;
        }

        // Thought is either a new thought on a newly-populated thoughtword or a thought
        // with an edited thoughtword that might be changing the list of all populated thoughtwords

        // Get and cache new list now, so the slight delay is experienced by the poster, not the next viewer
        Cache::delete('populatedThoughtwords');
        $thoughtsTable->getWords();
    }

    /**
     * Updates the cache of populated thoughtwords and clears TTS data
     *
     * @param Event $event Event
     * @param Thought $thought Entity
     */
    public function thoughtUpdated($event, $thought)
    {
        if ($thought->tts) {
            $this->removeTTS($thought);
        }

        $this->updatePopulatedThoughtwords($event, $thought);
    }

    /**
     * Removes thought text-to-speech file
     *
     * @param Thought $thought Entity
     */
    private function removeTTS(Thought $thought)
    {
        unlink(TTS::PATH . $thought->tts);
        $thoughtsTable = TableRegistry::getTableLocator()->get('Thoughts');
        $thoughtsTable->patchEntity($thought, ['tts' => null]);
        $thoughtsTable->save($thought);
    }

    /**
     * Updates the cache of populated thoughtwords and clears TTS data
     *
     * @param Event $event Event
     * @param Thought $thought Entity
     */
    public function thoughtDeleted($event, $thought)
    {
        if ($thought->tts) {
            $this->removeTTS($thought);
        }

        $this->updatePopulatedThoughtwords($event, $thought);
    }
}
