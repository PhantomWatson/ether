<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

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
        'color' => true,
        'comment' => true,
        'time' => true,
        'parsedTextCache' => true,
        'cacheTimestamp' => true,
        'thought' => true,
        'user' => true,
    ];
}
