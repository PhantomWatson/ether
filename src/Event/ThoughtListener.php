<?php
namespace App\Event;

use Cake\Event\EventListenerInterface;
use Cake\ORM\TableRegistry;

class ThoughtListener implements EventListenerInterface
{

	public function implementedEvents()
	{
		return [
			'Model.Thought.created' => [
				'updatePopulatedThoughtwords',
				'parseThought'
			]
		];
	}

	public function updatePopulatedThoughtwords($event, $entity, $options)
	{
		// new thought word is $event->data['word']

		// Check to see if the count of thoughts with this word is 1
		// If so, it's a new addition to the list of populated thoughtwords
		// and the cached list should be cleared and rewritten here (so the slight delay is experienced by the poster, not the next viewer)
	}

	public function parseThought($event, $entity, $options)
	{
		// Don't bother re-parsing thought if it hasn't changed
		if (! $entity->dirty('thought')) {
			return;
		}

		$thoughts = TableRegistry::get('Thoughts');
		$parsedThought = $tables->linkThoughtwords($event->data['thought']);
		$entity->set('parsedTextCache', $parsedThought);
		$thoughts->save($entity);
	}
}