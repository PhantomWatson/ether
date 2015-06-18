<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

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

    public function _getFormattedThought($formattedThought)
    {
        $thought = $thoughts->find('all')->where(['id' => $this->_properties['id']])->first();
        if ($this->needsReformatting($formattedThought, $thought)) {
            $thoughts = TableRegistry::get('Thoughts');
            $formattedThought = $thoughts->formatThought($this->_properties['thought']);
            $thought->formatted_thought = $formattedThought;
            $thought->formatting_key = Cache::read('populatedThoughtwordHash');
            $thoughts->save($thought);
        }
        return $formattedThought;
    }

    /**
     * Returns TRUE if the thought has never been formatted, or if its formatting
     * key (hash of list of populated thoughtwords) is out of date.
     *
     * @param $formattedThought string
     * @param $thought Entity
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
