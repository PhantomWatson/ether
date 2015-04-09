<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;

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
		return $this->find('all')
			->select([
				'id',
				'color',
				'count' => $this->find()->func()->count('Thoughts.id')
			])
			->matching('Thoughts')
			->group(['Users.id'])
			->having(['count >' => 0])
			->toArray();
	}
}