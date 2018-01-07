<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;
use Cake\Auth\DefaultPasswordHasher;

/**
 * User Entity.
 *
 * @property int $id
 * @property string $password
 * @property int $password_version
 * @property bool $is_admin
 * @property string $email
 * @property string $color
 * @property int $messageNotification
 * @property string $profile
 * @property int $acceptMessages
 * @property int $emailUpdates
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property \App\Model\Entity\Comment[] $comments
 * @property \App\Model\Entity\Thought[] $thoughts
 */
class User extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'password' => true,
        'password_version' => true,
        'is_admin' => true,
        'email' => true,
        'color' => true,
        'messageNotification' => true,
        'profile' => true,
        'acceptMessages' => true,
        'emailUpdates' => true,
        'comments' => true,
        'thoughts' => true,
        'new_password' => true,
        'confirm_password' => true
    ];

    protected function _setPassword($password)
    {
        return (new DefaultPasswordHasher)->hash($password);
    }
}
