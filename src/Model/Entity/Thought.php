<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\Cache\Cache;
use Cake\I18n\Time;
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
        'comments_enabled' => true,
        'anonymous' => true,
    ];
    public $max_thoughtword_length = 30;

    public function _setThought($thought)
    {
        $thoughtsTable = TableRegistry::get('Thoughts');
        $formattedThought = $thoughtsTable->formatThought($thought);
        $this->set('formatted_thought', $formattedThought);
        return $thought;
    }

    public function _setFormattedThought($formattedThought)
    {
        $thoughtsTable = TableRegistry::get('Thoughts');
        $hash = $thoughtsTable->getPopulatedThoughtwordHash();
        $this->set('formatting_key', $hash);
        $this->set('formatted', Time::now());
        return $formattedThought;
    }

    public function _setWord($word)
    {
        $thoughtsTable = TableRegistry::get('Thoughts');
        return $thoughtsTable->formatThoughtword($word);
    }
}
