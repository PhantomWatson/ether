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
}
