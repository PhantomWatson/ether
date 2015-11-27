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
        'recipient_id' => true,
        'sender_id' => true,
        'message' => true,
        'received' => true,
        'recipient' => true,
        'sender' => true
    ];
}
