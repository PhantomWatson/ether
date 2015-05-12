<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;
use Cake\Collection\Collection;
use Cake\Network\Exception\InternalErrorException;

/**
 * Users Model
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
        $this->table('users');
        $this->displayField('id');
        $this->primaryKey('id');
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
            ->requirePresence('username', 'create')
            ->notEmpty('username')
            ->allowEmpty('password')
            ->add('password_version', 'valid', ['rule' => 'numeric'])
            ->requirePresence('password_version', 'create')
            ->notEmpty('password_version')
            ->allowEmpty('userid')
            ->add('userlevel', 'valid', ['rule' => 'boolean'])
            ->requirePresence('userlevel', 'create')
            ->notEmpty('userlevel')
            ->add('is_admin', 'valid', ['rule' => 'boolean'])
            ->requirePresence('is_admin', 'create')
            ->notEmpty('is_admin')
            ->add('email', 'valid', ['rule' => 'email'])
            ->requirePresence('email', 'create')
            ->notEmpty('email')
            ->add('timestamp', 'valid', ['rule' => 'numeric'])
            ->requirePresence('timestamp', 'create')
            ->notEmpty('timestamp')
            ->requirePresence('color', 'create')
            ->notEmpty('color')
            ->add('messageNotification', 'valid', ['rule' => 'numeric'])
            ->requirePresence('messageNotification', 'create')
            ->notEmpty('messageNotification')
            ->requirePresence('profile', 'create')
            ->notEmpty('profile')
            ->add('acceptMessages', 'valid', ['rule' => 'numeric'])
            ->requirePresence('acceptMessages', 'create')
            ->notEmpty('acceptMessages')
            ->add('emailUpdates', 'valid', ['rule' => 'numeric'])
            ->requirePresence('emailUpdates', 'create')
            ->notEmpty('emailUpdates')
            ->add('newMessages', 'valid', ['rule' => 'numeric'])
            ->requirePresence('newMessages', 'create')
            ->notEmpty('newMessages');

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
        $rules->add($rules->isUnique(['username']));
        $rules->add($rules->isUnique(['email']));
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
            		return $q
            			->select(['id', 'word', 'user_id'])
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
			->hydrate(false);
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
		$sorted_groups = array(
			'red' => [],
			'yellow' => [],
			'green' => [],
			'cyan' => [],
			'blue' => [],
			'purple' => [],
			'gray' => []
		);

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
     * Deactivates the "you have new messages" flag for the selected user
     * @param int $userId
     */
    public function setMessagesRead($userId)
    {
        $user = $this->get($userId);
        $user->newMessages = 0;
        $this->save($user);
    }

    /**
     * Activates the "you have new messages" flag for the selected user
     * @param int $userId
     */
    public function setMessagesUnread($userId)
    {
        $user = $this->get($userId);
        $user->newMessages = 1;
        $this->save($user);
    }
}