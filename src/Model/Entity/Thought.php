<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

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
        'parsedTextCache' => true,
        'cacheTimestamp' => true,
        'anonymous' => true,
        'user' => true,
        'comments' => true,
    ];
}
