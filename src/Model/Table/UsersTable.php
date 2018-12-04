<?php
namespace App\Model\Table;

use Cake\Collection\Collection;
use Cake\Http\Exception\InternalErrorException;
use Cake\Mailer\Email;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Routing\Router;
use Cake\Utility\Security;
use Cake\Validation\Validator;
use League\HTMLToMarkdown\HtmlConverter;

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
    public function initialize(array $config)
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
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator instance
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('id', 'create')
            ->allowEmpty('password')
            ->add('password_version', 'valid', ['rule' => 'numeric'])
            ->requirePresence('password_version', 'create')
            ->notEmpty('password_version')
            ->add('is_admin', 'valid', ['rule' => 'boolean'])
            ->requirePresence('is_admin', 'create')
            ->notEmpty('is_admin')
            ->add('email', 'valid', ['rule' => 'email'])
            ->requirePresence('email', 'create')
            ->notEmpty('email')
            ->requirePresence('color', 'create')
            ->notEmpty('color')
            ->add('color', 'validColor', [
                'rule' => function ($value) {
                    return (boolean) preg_match('/^[a-fA-F0-9]{6}$/', $value);
                },
                'message' => 'That does not appear to be a valid hexadecimal color'
            ])
            ->add('messageNotification', 'valid', ['rule' => 'boolean'])
            ->allowEmpty('messageNotification')
            ->add('acceptMessages', 'valid', ['rule' => 'boolean'])
            ->allowEmpty('acceptMessages')
            ->add('emailUpdates', 'valid', ['rule' => 'boolean'])
            ->allowEmpty('emailUpdates')
            ->notEmpty('new_password')
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
    public function buildRules(RulesChecker $rules)
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
        return $this->findByColor($color)
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
                        ])
                        ->distinct(['word']);
                }
            ])
            ->first()
            ->toArray();
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
     * Removs slashes that were a leftover of the anti-injection-attack strategy of the olllllld Ether
     */
    public function overhaulStripSlashes()
    {
        $users = $this->find('all')
            ->select(['id', 'profile'])
            ->where(['profile LIKE' => '%\\\\%'])
            ->order(['id' => 'ASC']);
        foreach ($users as $user) {
            echo $user->profile;
            $fixed = stripslashes($user->profile);
            $user->profile = $fixed;
            $this->save($user);
            echo " => $fixed<br />";
        }
    }

    public function overhaulToMarkdown()
    {
        $field = 'profile';
        $results = $this->find('all')
            ->select(['id', $field])
            ->where([
                "$field LIKE" => '%<%',
                'markdown' => false
            ])
            ->order(['id' => 'ASC']);
        if ($results->count() == 0) {
            echo "No {$field}s to convert";
        }
        foreach ($results as $result) {
            $converter = new HtmlConverter(['strip_tags' => false]);
            $markdown = $converter->convert($result->$field);
            $result->$field = $markdown;
            $result->markdown = true;
            if ($this->save($result)) {
                echo "Converted $field #$result->id<br />";
            } else {
                echo "ERROR converting $field #$result->id<br />";
            }
        }
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
     * @return array
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
        $email = new Email('reset_password');
        $user = $this->get($userId);
        $email->setTo($user->email);
        $email->setViewVars(compact(
            'resetUrl',
            'siteUrl'
        ));

        return $email->send();
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
}
