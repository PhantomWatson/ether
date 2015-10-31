<?php
namespace App\Model\Entity;

use Cake\I18n\Time;
use Cake\ORM\Entity;
use Cake\ORM\TableRegistry;

/**
 * Comment Entity.
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
        'comment' => true
    ];

    public function _setComment($comment)
    {
        $thoughtsTable = TableRegistry::get('Thoughts');
        $formatted = $thoughtsTable->formatThought($comment);
        $this->set('formatted_comment', $formatted);
        return $comment;
    }

    public function _setFormattedComment($formattedComment)
    {
        $thoughtsTable = TableRegistry::get('Thoughts');
        $hash = $thoughtsTable->getPopulatedThoughtwordHash();
        $this->set('formatting_key', $hash);
        $this->set('formatted', Time::now());
        return $formattedComment;
    }
}
