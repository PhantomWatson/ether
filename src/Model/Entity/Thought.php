<?php
namespace App\Model\Entity;

use App\Model\Table\ThoughtsTable;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;

/**
 * Thought Entity.
 *
 * @property \App\Model\Entity\Comment[] $comments
 * @property \App\Model\Entity\User $user
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $formatted
 * @property \Cake\I18n\FrozenTime $modified
 * @property bool $anonymous
 * @property bool $comments_enabled
 * @property bool $hidden
 * @property int $id
 * @property int $user_id
 * @property string $formatted_thought
 * @property string $formatting_key
 * @property string $thought
 * @property string $tts Text-to-speech audio filename
 * @property string $word
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
        'hidden' => true,
        'tts' => true,
    ];
    public $max_thoughtword_length = 30;

    public function _setThought($thought)
    {
        /** @var ThoughtsTable $thoughtsTable */
        $thoughtsTable = TableRegistry::getTableLocator()->get('Thoughts');
        $formattedThought = $thoughtsTable->formatThought($thought);
        $this->set('formatted_thought', $formattedThought);
        return $thought;
    }

    public function _setFormattedThought($formattedThought)
    {
        /** @var ThoughtsTable $thoughtsTable */
        $thoughtsTable = TableRegistry::getTableLocator()->get('Thoughts');
        $hash = $thoughtsTable->getPopulatedThoughtwordHash();
        $this->set('formatting_key', $hash);
        $this->set('formatted', Time::now());
        return $formattedThought;
    }

    public function _setWord($word)
    {
        /** @var ThoughtsTable $thoughtsTable */
        $thoughtsTable = TableRegistry::getTableLocator()->get('Thoughts');
        return $thoughtsTable->formatThoughtword($word);
    }

    /**
     * Returns all of the sentences that end with question marks in this thought
     *
     * @return array
     */
    protected function _getQuestions()
    {
        $questions = [];
        $sentences = preg_split('/(?<=[.?!])\s+(?=[a-z])/i', $this->_properties['thought']);
        foreach ($sentences as $sentence) {
            if (strpos($sentence, '?') === strlen($sentence) - 1) {
                $questions[] = $sentence;
            }
        }

        return $questions;
    }
}
