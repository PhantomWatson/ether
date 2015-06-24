<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;
use Cake\Log\Log;

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
        'formatted_thought' => true,
        'cacheTimestamp' => true,
        'anonymous' => true,
        'user' => true,
        'comments' => true,
        'parsed' => true
    ];
    public $max_thoughtword_length = 30;

    /**
     * Automatically formats thought if it has not been formatted,
     * or reformats thought if its formatting is out of date
     *
     * @param string $formattedThought
     * @return string
     */
    public function _getFormattedThought($formattedThought)
    {
        $thoughtsTable = TableRegistry::get('Thoughts');
        $thought = $thoughtsTable->get($this->_properties['id']);
        if ($this->needsReformatting($formattedThought, $thought)) {
            Log::write('info', 'Thought '.$this->_properties['id'].'\'s formatting is out-of-date');
            return $thoughtsTable->formatThought($this->_properties['thought']);
        }
        return $formattedThought;
    }

    public function _setFormattedThought($formattedThought)
    {
        $thoughtsTable = TableRegistry::get('Thoughts');
        $hash = $thoughtsTable->getPopulatedThoughtwordHash();
        $this->set('formatting_key', $hash);
        return $formattedThought;
    }

    /**
     * Returns TRUE if the thought has never been formatted, or if its formatting
     * key (hash of list of populated thoughtwords) is out of date.
     *
     * @param string $formattedThought
     * @param Entity $thought
     * @return boolean
     */
    private function needsReformatting($formattedThought, $thought)
    {
        if ($formattedThought === '' || $formattedThought === null) {
            return true;
        }

        $currentFormattingKey = Cache::read('populatedThoughtwordHash');
        if ($currentFormattingKey && $thought->formatting_key != $currentFormattingKey) {
            return true;
        }

        return false;
    }
}
