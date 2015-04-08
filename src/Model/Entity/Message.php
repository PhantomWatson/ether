<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Message Entity.
 */
class Message extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'recipient' => true,
        'recipient_id' => true,
        'availableToRecipient' => true,
        'sender' => true,
        'sender_id' => true,
        'availableToSender' => true,
        'status' => true,
        'timeSent' => true,
        'message' => true,
        'parsedTextCache' => true,
        'cacheTimestamp' => true,
        'received' => true,
        'recipient' => true,
        'sender' => true,
    ];
}
