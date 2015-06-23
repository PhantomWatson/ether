<?php
namespace App\Event;

use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;

class ThoughtListener implements EventListenerInterface
{

    public function implementedEvents()
    {
        return [
            'Model.Thought.created' => [
                ['callable' => 'updatePopulatedThoughtwords'],
                ['callable' => 'formatThought']
            ],
            'Model.Thought.updated' => [
                ['callable' => 'updatePopulatedThoughtwords'],
                ['callable' => 'formatThought']
            ],
            'Model.Thought.deleted' => [
                ['callable' => 'updatePopulatedThoughtwords']
            ]
        ];
    }

    public function updatePopulatedThoughtwords($event, $entity, $options)
    {
        // Exit if entity was updated without changing word
        if (! $entity->isNew() && ! $entity->dirty('word')) {
            return;
        }

        // Exit if this is a new thought on an already-populated thoughtword
        $thoughts = TableRegistry::get('Thoughts');
        if ($entity->isNew() && $thoughts->getPopulation($entity->word) > 1) {
            return;
        }

        // Thought is either a new thought on a newly-populated thoughtword or a thought
        // with an edited thoughtword that might be changing the list of all populated thoughtwords

        // Get and cache new list now, so the slight delay is experienced by the poster, not the next viewer
        Cache::delete('populatedThoughtwords');
        $thoughts->getWords();
    }

    public function formatThought($event, $entity, $options)
    {
        // Don't bother reformatting thought if it hasn't changed
        if (! $entity->isNew() && ! $entity->dirty('thought')) {
            return;
        }

        $thoughtsTable = TableRegistry::get('Thoughts');
        $formattedThought = $thoughtsTable->formatThought($entity->get('thought'));
        $entity->set('formatted_thought', $formattedThought);
        $entity->set('formatted', Time::now());
        $thoughtsTable->save($entity);
    }
}
