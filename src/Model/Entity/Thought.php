<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Thought Entity.
 */
class Thought extends Entity
{

	/**
	 * Fields that can be mass assigned using newEntity() or patchEntity().
	 *
	 * @var array
	 */
	protected $_accessible = [
		'user_id' => true,
		'word' => true,
		'thought' => true,
		'color' => true,
		'time' => true,
		'edited' => true,
		'comments_enabled' => true,
		'parsedTextCache' => true,
		'cacheTimestamp' => true,
		'anonymous' => true,
		'user' => true,
		'comments' => true,
	];
	public $max_thoughtword_length = 30;

	public function afterSave($created, $options = array()) {
		if ($created) {
			Cache::increment('getCount()');
		}
	}
	
	/**
	 * Converts $word into a valid thoughtword (alphanumeric, no spaces, max 30 characters)
	 * @param string $word
	 * @return string
	 */
	public function format_thoughtword($word) {
		$word = preg_replace('/[^a-zA-Z0-9]/', '', $word);
		if (strlen($word) > $this->max_thoughtword_length) {
			$word = substr($word, 0, $this->max_thoughtword_length);
		}
		return strtolower($word);
	}
	
	/**
	 * Checks to see if the thought in $this->request->data is already in the database
	 * @return int|boolean Either the ID of the existing thought or FALSE
	 */
	public function isDuplicate() {
		$user_id = $this->request->data['Thought']['user_id'];
		$thought = $this->request->data['Thought']['thought'];
		$thoughts = TableRegistry::get('Thoughts');
		$query = $thoughts->findByUserIdAndThought($user_id, $thought);
		$query
			->select(['id'])
			->order(['Thought.created' => 'DESC'])
			->first();
		$results = $query->toArray();
		return isset($results['Thought']['id']) ? $results['Thought']['id'] : false;
	}
}
