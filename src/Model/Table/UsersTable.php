<?php
namespace App\Model\Table;

use App\Application;
use Cake\Collection\Collection;
use Cake\Database\Expression\QueryExpression;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\InternalErrorException;
use Cake\Mailer\Email;
use Cake\Mailer\Mailer;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Routing\Router;
use Cake\Utility\Hash;
use Cake\Utility\Security;
use Cake\Validation\Validator;

/**
 * Users Model
 * @method Query findByColor($color)
 * @property \App\Model\Table\CommentsTable|\Cake\ORM\Association\HasMany $Comments
 * @property \App\Model\Table\ThoughtsTable|\Cake\ORM\Association\HasMany $Thoughts
 * @method \App\Model\Entity\User get($primaryKey, $options = [])
 * @method \App\Model\Entity\User newEntity($data = null, array $options = [])
 * @method \App\Model\Entity\User[] newEntities(array $data, array $options = [])
 * @method \App\Model\Entity\User|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \App\Model\Entity\User patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \App\Model\Entity\User[] patchEntities($entities, array $data, array $options = [])
 * @method \App\Model\Entity\User findOrCreate($search, callable $callback = null, $options = [])
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class UsersTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        $this->setTable('users');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
        $this->hasMany('Comments', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('Thoughts', [
            'foreignKey' => 'user_id'
        ]);
        $this->hasMany('SentMessages', [
            'foreignKey' => 'sender_id',
            'className' => 'Messages',
        ]);
        $this->hasMany('ReceivedMessages', [
            'foreignKey' => 'recipient_id',
            'className' => 'Messages',
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator instance
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): \Cake\Validation\Validator
    {
        $validator
            ->scalar('id')
            ->numeric('id');

        $validator
            ->scalar('password')
            ->notEmptyString('password');

        $validator
            ->scalar('password_version')
            ->numeric('password_version')
            ->requirePresence('password_version', 'create')
            ->notEmptyString('password_version');

        $validator
            ->scalar('is_admin')
            ->boolean('is_admin')
            ->requirePresence('is_admin', 'create');

        $validator
            ->scalar('email')
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmptyString('email');

        $validator
            ->scalar('color')
            ->requirePresence('color', 'create')
            ->notEmptyString('color')
            ->add('color', 'validColor', [
                'rule' => function ($value) {
                    return (boolean) preg_match('/^[a-fA-F0-9]{6}$/', $value);
                },
                'message' => 'That does not appear to be a valid hexadecimal color'
            ]);

        $validator
            ->scalar('messageNotification')
            ->boolean('messageNotification');

        $validator
            ->scalar('acceptMessages')
            ->boolean('acceptMessages');

        $validator
            ->scalar('emailUpdates')
            ->boolean('emailUpdates');

        $validator
            ->scalar('new_password')
            ->notEmptyString('new_password');

        $validator
            ->scalar('confirm_password')
            ->notEmptyString('confirm_password')
            ->add('confirm_password', 'compareWith', [
                'rule' => ['compareWith', 'new_password'],
                'message' => 'Passwords do not match.'
            ]);

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): \Cake\ORM\RulesChecker
    {
        $rules->add($rules->isUnique(['email'], [
            'message' => 'Someone already registered with this email address. Probably you, I guess.'
        ]));
        return $rules;
    }

    /**
     * Checks whether or not a color is already assigned to a user
     * @param string $color
     * @return boolean
     */
    public function colorIsTaken($color)
    {
        $color = str_replace('#', '', $color);
        $query = $this->find('all')
            ->where(['color' => $color]);
        return $query->count() > 0;
    }

    /**
     * Returns an array of the data needed for a user's profile
     * @param string $color
     * @return array
     * @throws \Cake\Network\Exception\NotFoundException
     */
    public function getProfileInfo($color)
    {
        $user = $this->findByColor($color)
            ->select([
                'id',
                'color',
                'acceptMessages',
                'profile'
            ])
            ->contain([
                'Thoughts' => function ($q) {
                    /** @var Query $q */

                    return $q
                        ->select(['id', 'word', 'user_id'])
                        ->where(['anonymous' => false])
                        ->order([
                            'word' => 'ASC',
                            'created' => 'DESC'
                        ]);
                }
            ])
            ->first()
            ->toArray();

        $uniqueWords = [];
        foreach ($user['thoughts'] as $k => $thought) {
            if (in_array($thought['word'], $uniqueWords)) {
                unset($user['thoughts'][$k]);
            } else {
                $uniqueWords[] = $thought['word'];
            }
        }

        return $user;
    }

    /**
     * Returns an array of all colors with associated thoughts
     *
     * @return array
     * @throws InternalErrorException
     */
    public function getColorsWithThoughts()
    {
        $result = $this->find('all')
            ->select([
                'color',
                'count' => $this->find()->func()->count('Thoughts.id')
            ])
            ->matching('Thoughts')
            ->group(['Users.id'])
            ->having(['count >' => 0])
            ->enableHydration(false);
        $collection = new Collection($result);
        $combined = $collection->combine('color', 'count');
        $colors = $combined->toArray();
        $sorted = $this->sortColors($colors);

        return $sorted;
    }

    /**
     * Takes an array of color => thoughtCount and groups it into sorted red, green, blue, etc. categories
     * @param array $colors
     * @return array
     * @throws \Cake\Network\Exception\InternalErrorException
     */
    public function sortColors($colors)
    {
        $tolerance = 40;
        $hex_codes = [];
        $colors_categorized = [];
        $sorted_groups = [
            'red' => [],
            'yellow' => [],
            'green' => [],
            'cyan' => [],
            'blue' => [],
            'purple' => [],
            'gray' => []
        ];

        foreach ($colors as $color => $count) {
            $color_split = str_split($color, 2);
            $this_color = [];
            $this_color['count'] = $count;

            // Figure out what sort of color this is
            $this_color['red'] = $red = hexdec($color_split[0]);
            $this_color['green'] = $green = hexdec($color_split[1]);
            $this_color['blue'] = $blue = hexdec($color_split[2]);
            $yellow = abs($red- $green);
            $cyan = abs($green - $blue);
            $purple = abs($blue - $red);
            if ($red== $green && $green == $blue) {
                $category = 'gray';
            } elseif ($yellow < $tolerance && $red> $blue && $green > $blue) {
                $category = 'yellow';
            } elseif ($cyan < $tolerance && $blue > $red && $green > $red) {
                $category = 'cyan';
            } elseif ($purple < $tolerance && $red > $green && $blue > $green) {
                $category = 'purple';
            } elseif ($red > $green && $red > $blue) {
                $category = 'red';
            } elseif ($green > $red && $green > $blue) {
                $category = 'green';
            } elseif ($blue > $green && $blue > $red) {
                $category = 'blue';
            } else {
                throw new InternalErrorException("Error with color #$color");
            }
            $colors_categorized[$category][] = $this_color;

            $rgb = $this_color['red'].' '.$this_color['green'].' '.$this_color['blue'];
            $hex_codes[$rgb] = $color;
        }
        $group_key = 0;
        foreach ($colors_categorized as $category => $category_colors) {
            $sorted_group = [];
            foreach ($category_colors as $color) {
                $rgb = $color['red'].' '.$color['green'].' '.$color['blue'];
                $hex = $hex_codes[$rgb];
                $dec = hexdec($hex);
                $sorted_group[$dec] = [
                    'color' => $hex,
                    'count' => $color['count']
                ];
            }
            if ($group_key % 2 == 1) {
                krsort($sorted_group);
            } else {
                ksort($sorted_group);
            }
            $collection = new Collection($sorted_group);
            $combined = $collection->combine('color', 'count');
            $sorted_groups[$category] = $combined->toArray();

            $group_key++;
        }
        return $sorted_groups;
    }

    /**
     * Returns true if the user has configured their account to accept messages
     *
     * @param int $userId
     * @return boolean
     */
    public function acceptsMessages($userId)
    {
        $user = $this->get($userId);
        return (boolean) $user->acceptMessages;
    }

    public function getIdFromColor($color)
    {
        return $this->findByColor($color)
            ->select(['id'])
            ->first()
            ->id;
    }

    public function getActiveThinkerCount()
    {
        return $this->Thoughts->find('all')->select(['user_id'])->distinct(['user_id'])->count();
    }

    /**
     * Returns the number of users who have posted thoughts but not comments or sent messages
     *
     * @return int
     */
    public function getOnlyPostedThoughtsCount(): int
    {
        // Couldn't manage to accomplish this with a single query :(
        $usersWithComments = $this
            ->find()
            ->select(['Users.id'])
            ->distinct('Users.id')
            ->matching('Comments');
        $usersWithSendMessages = $this
            ->find()
            ->select(['Users.id'])
            ->distinct('Users.id')
            ->matching('SentMessages');

        return $this
            ->find()
            ->select(['Users.id'])
            ->distinct(['Users.id'])
            ->matching('Thoughts')
            ->where(function (QueryExpression $exp) use ($usersWithComments, $usersWithSendMessages) {
                return $exp
                    ->notIn('Users.id', Hash::extract($usersWithComments->toArray(), '{n}.id'))
                    ->notIn('Users.id', Hash::extract($usersWithSendMessages->toArray(), '{n}.id'));
            })
            ->count();
    }

    /**
     * Returns the number of users who have posted comments but not thoughts or sent messages
     *
     * @return int
     */
    public function getOnlyPostedCommentsCount(): int
    {
        // Couldn't manage to accomplish this with a single query :(
        $usersWithThoughts = $this
            ->find()
            ->select(['Users.id'])
            ->distinct('Users.id')
            ->matching('Thoughts');
        $usersWithSendMessages = $this
            ->find()
            ->select(['Users.id'])
            ->distinct('Users.id')
            ->matching('SentMessages');

        return $this
            ->find()
            ->select(['Users.id'])
            ->distinct(['Users.id'])
            ->matching('Comments')
            ->where(function (QueryExpression $exp) use ($usersWithThoughts, $usersWithSendMessages) {
                return $exp
                    ->notIn('Users.id', Hash::extract($usersWithThoughts->toArray(), '{n}.id'))
                    ->notIn('Users.id', Hash::extract($usersWithSendMessages->toArray(), '{n}.id'));
            })
            ->count();
    }

    /**
     * Returns the number of users who have sent messages but not posted thoughts or comments
     *
     * @return int
     */
    public function getOnlySentMessagesCount(): int
    {
        // Couldn't manage to accomplish this with a single query :(
        $usersWithThoughts = $this
            ->find()
            ->select(['Users.id'])
            ->distinct('Users.id')
            ->matching('Thoughts');
        $usersWithComments = $this
            ->find()
            ->select(['Users.id'])
            ->distinct('Users.id')
            ->matching('Comments');

        return $this
            ->find()
            ->select(['Users.id'])
            ->distinct(['Users.id'])
            ->matching('SentMessages')
            ->where(function (QueryExpression $exp) use ($usersWithThoughts, $usersWithComments) {
                return $exp
                    ->notIn('Users.id', Hash::extract($usersWithThoughts->toArray(), '{n}.id'))
                    ->notIn('Users.id', Hash::extract($usersWithComments->toArray(), '{n}.id'));
            })
            ->count();
    }

    /**
     * Returns the number of inactive users
     *
     * @return int
     */
    public function getInactiveCount(): int
    {
        // Couldn't manage to accomplish this with a single query :(
        $usersWithThoughts = $this
            ->find()
            ->select(['Users.id'])
            ->distinct('Users.id')
            ->matching('Thoughts');
        $usersWithComments = $this
            ->find()
            ->select(['Users.id'])
            ->distinct('Users.id')
            ->matching('Comments');
        $usersWithSentMessages = $this
            ->find()
            ->select(['Users.id'])
            ->distinct('Users.id')
            ->matching('SentMessages');

        return $this
            ->find()
            ->select(['Users.id'])
            ->distinct(['Users.id'])
            ->where(function (QueryExpression $exp) use ($usersWithThoughts, $usersWithComments, $usersWithSentMessages) {
                return $exp
                    ->notIn('Users.id', Hash::extract($usersWithThoughts->toArray(), '{n}.id'))
                    ->notIn('Users.id', Hash::extract($usersWithComments->toArray(), '{n}.id'))
                    ->notIn('Users.id', Hash::extract($usersWithSentMessages->toArray(), '{n}.id'));
            })
            ->count();
    }

    /**
     * @param string $email
     * @return int|null
     */
    public function getIdWithEmail($email)
    {
        $user = $this->find('all')
            ->select(['id'])
            ->where(['email' => $email])
            ->limit(1);
        if ($user->isEmpty()) {
            return null;
        }
        return $user->first()->id;
    }

    /**
     * Sends an email with a link that can be used in the next
     * 24 hours to give the user access to /users/resetPassword
     *
     * @param int $userId User ID
     * @return void
     */
    public function sendPasswordResetEmail($userId)
    {
        $timestamp = time();
        $hash = $this->getPasswordResetHash($userId, $timestamp);
        $resetUrl = Router::url([
            'controller' => 'Users',
            'action' => 'resetPassword',
            $userId,
            $timestamp,
            $hash
        ], true);
        $siteUrl = Router::url('/', true);
        $user = $this->get($userId);
        $mailer = new Mailer();
        $mailer
            ->setEmailFormat(\Cake\Mailer\Message::MESSAGE_BOTH)
            ->setTo($user->email)
            ->setFrom(Application::EMAIL_FROM)
            ->setSender(Application::EMAIL_FROM)
            ->setSubject('Ether Account Password Reset')
            ->viewBuilder()
                ->setTemplate('reset_password')
                ->setVars(compact(
                    'resetUrl',
                    'siteUrl'
                ));
        $mailer->deliver();
    }

    /**
     * Returns a hash for use in the emailed link to /reset-password
     *
     * @param int $userId
     * @param int $timestamp
     * @return string
     */
    public function getPasswordResetHash($userId, $timestamp)
    {
        return Security::hash($userId.$timestamp, 'sha1', true);
    }

    /**
     * Fetches users with no evidence of their interaction with the site
     *
     * @param Query $query
     * @return Query
     * @throws BadRequestException
     */
    public function findInactive(Query $query)
    {
        return $query
            ->notMatching('Thoughts')
            ->notMatching('Comments')
            ->notMatching('SentMessages')
            ->notMatching('ReceivedMessages')
            ->where([
                'profile' => '',
                'is_admin' => false,
            ]);
    }

    /**
     * Returns a set of inactive users that registered > $gracePeriod ago
     *
     * @return \Cake\Datasource\ResultSetInterface|\App\Model\Entity\User[]
     */
    public function getInactiveUsersToPrune($gracePeriod = '6 months')
    {
        return $this
            ->find('inactive')
            ->where([
                'OR' => [
                    function (QueryExpression $exp) use ($gracePeriod) {
                        return $exp->lte('Users.created', new \DateTime("-$gracePeriod"));
                    },
                    function (QueryExpression $exp) {
                        return $exp->isNull('Users.created');
                    },
                ]
            ])
            ->all();
    }
}
