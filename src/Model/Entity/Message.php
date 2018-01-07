<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Message Entity.
 *
 * @property int $id
 * @property int $recipient_id
 * @property int $sender_id
 * @property string $message
 * @property bool $received
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property \App\Model\Entity\User $recipient
 * @property \App\Model\Entity\User $sender
 */
class Message extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'recipient_id' => true,
        'sender_id' => true,
        'message' => true,
        'received' => true,
        'recipient' => true,
        'sender' => true
    ];
}
