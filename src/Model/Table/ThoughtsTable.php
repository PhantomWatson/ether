<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\Collection\Collection;

/**
 * Thoughts Model
 */
class ThoughtsTable extends Table
{

	/**
	 * Initialize method
	 *
	 * @param array $config The configuration for the Table.
	 * @return void
	 */
	public function initialize(array $config)
	{
		$this->table('thoughts');
		$this->displayField('id');
		$this->primaryKey('id');
		$this->addBehavior('Timestamp');
		$this->belongsTo('Users', [
			'foreignKey' => 'user_id'
		]);
		$this->hasMany('Comments', [
			'foreignKey' => 'thought_id'
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
			->add('user_id', 'valid', ['rule' => 'numeric'])
			->allowEmpty('user_id')
			->requirePresence('word', 'create')
			->notEmpty('word')
			->requirePresence('thought', 'create')
			->add('thought', [
				'length' => [
					'rule' => ['minLength', 20],
					'message' => 'That thought is way too short! Please enter at least 20 characters.'
				]
			])
			->requirePresence('color', 'create')
			->notEmpty('color')
			->add('time', 'valid', ['rule' => 'numeric'])
			->requirePresence('time', 'create')
			->notEmpty('time')
			->add('edited', 'valid', ['rule' => 'numeric'])
			->requirePresence('edited', 'create')
			->notEmpty('edited')
			->add('comments_enabled', 'valid', ['rule' => 'numeric'])
			->requirePresence('comments_enabled', 'create')
			->notEmpty('comments_enabled')
			->requirePresence('parsedTextCache', 'create')
			->notEmpty('parsedTextCache')
			->add('cacheTimestamp', 'valid', ['rule' => 'numeric'])
			->requirePresence('cacheTimestamp', 'create')
			->notEmpty('cacheTimestamp')
			->add('anonymous', 'valid', ['rule' => 'boolean'])
			->requirePresence('anonymous', 'create')
			->notEmpty('anonymous');

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
		$rules->add($rules->existsIn(['user_id'], 'Users'));
		return $rules;
	}
	
	/**
	 * Returns an alphabetized list of all unique thoughtwords
	 * @return array
	 */
	public function getWords() {
		return $this
			->find('all')
			->select(['word'])
			->distinct(['word'])
			->order(['word' => 'ASC'])
			->extract('word')
			->toArray();
	}
}
