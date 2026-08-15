<?php
namespace App\Model\Entity;

use ArrayAccess;
use Authentication\IdentityInterface;
use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\ORM\Entity;

/**
 * User Entity.
 *
 * @property int $id
 * @property string|null $password
 * @property int $password_version
 * @property bool $is_admin
 * @property string $email
 * @property string $color
 * @property int $messageNotification
 * @property string|null $profile
 * @property int $acceptMessages
 * @property int $emailUpdates
 * @property \Cake\I18n\DateTime|null $created
 * @property \Cake\I18n\DateTime|null $modified
 * @property \App\Model\Entity\Comment[] $comments
 * @property \App\Model\Entity\Thought[] $thoughts
 * @property \App\Model\Entity\Message[] $sent_messages
 * @property \App\Model\Entity\Message[] $received_messages
 */
class User extends Entity implements IdentityInterface
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected array $_accessible = [
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

    public function getIdentifier(): array|string|int|null
    {
        return $this->id;
    }

    public function getOriginalData(): ArrayAccess|array
    {
        return $this;
    }
}
