<?php
namespace App\Model\Entity;

use App\Model\Table\ThoughtsTable;
use Cake\I18n\FrozenTime;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * Comment Entity.
 *
 * @property bool $anonymous
 * @property int $id
 * @property int $thought_id
 * @property int $user_id
 * @property string $comment
 * @property string $formatted_comment
 * @property string $formatting_key
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property \Cake\I18n\FrozenTime $formatted
 * @property \App\Model\Entity\Thought $thought
 * @property \App\Model\Entity\User $user
 */
class Comment extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'thought_id' => true,
        'user_id' => true,
        'comment' => true,
        'anonymous' => true
    ];

    public function _setComment($comment)
    {
        /** @var ThoughtsTable $thoughtsTable */
        $thoughtsTable = TableRegistry::getTableLocator()->get('Thoughts');
        $formatted = $thoughtsTable->formatThought($comment);
        $this->set('formatted_comment', $formatted);
        return $comment;
    }

    public function _setFormattedComment($formattedComment)
    {
        /** @var ThoughtsTable $thoughtsTable */
        $thoughtsTable = TableRegistry::getTableLocator()->get('Thoughts');
        $hash = $thoughtsTable->getPopulatedThoughtwordHash();
        $this->set('formatting_key', $hash);
        $this->set('formatted', FrozenTime::now());
        return $formattedComment;
    }
}
