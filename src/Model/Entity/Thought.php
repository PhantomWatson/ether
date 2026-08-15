<?php
namespace App\Model\Entity;

use App\Model\Table\ThoughtsTable;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;
use Cake\Routing\Router;

/**
 * Thought Entity.
 *
 * @property \App\Model\Entity\Comment[] $comments
 * @property \App\Model\Entity\User|null $user
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $formatted
 * @property \Cake\I18n\DateTime|null $modified
 * @property bool $anonymous
 * @property bool $comments_enabled
 * @property bool $hidden
 * @property int $id
 * @property int|null $user_id
 * @property string|null $formatted_thought
 * @property string|null $formatting_key
 * @property string $thought The body of the thought
 * @property string $tts Text-to-speech audio filename
 * @property string $word
 *
 * Virtual fields
 * @property string $url Full URL to the thought
 * @property-read string[] $questions
 */
class Thought extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected array $_accessible = [
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
        $this->set('formatted', \Cake\I18n\DateTime::now());
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
     * @return string[]
     * @see \App\Model\Entity\Thought::$questions
     */
    protected function _getQuestions(): array
    {
        $questions = [];
        $sentences = preg_split('/(?<=[.?!])\s+(?=[a-z])/i', $this->_fields['thought']);
        foreach ($sentences as $sentence) {
            if (strpos($sentence, '?') === strlen($sentence) - 1) {
                $questions[] = $sentence;
            }
        }

        return $questions;
    }

    protected function _getUrl(): string
    {
        return Router::url([
            'controller' => 'Thoughts',
            'action' => 'word',
            $this->word,
            '#' => 't' . $this->id
        ], true);
    }
}
