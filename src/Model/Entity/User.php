<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * User Entity.
 */
class User extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'username' => true,
        'password' => true,
        'password_version' => true,
        'userid' => true,
        'userlevel' => true,
        'is_admin' => true,
        'email' => true,
        'timestamp' => true,
        'color' => true,
        'messageNotification' => true,
        'profile' => true,
        'acceptMessages' => true,
        'emailUpdates' => true,
        'newMessages' => true,
        'comments' => true,
        'thoughts' => true,
    ];
}
