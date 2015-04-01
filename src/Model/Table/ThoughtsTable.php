<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
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

	/**
	 * Returns a list of the 300 most-populated thoughtwords and their thought counts
	 * @return array
	 */
	public function getTopCloud() {
		return $this->getCloud(300);
	}

	/**
	 * Returns a list of all thoughtwords and their thought counts
	 * @param int $limit
	 * @return array
	 */
	public function getCloud($limit = false) {
		$query = $this
			->find('list', [
				'keyField' => 'word',
				'valueField' => 'COUNT(*)'
			])
			->group('keyfield')
			->order(['word' => 'DESC']);
		if ($limit) {
			$query->limit($limit);
		}
		return $query->toArray();
	}

	/**
	 * Returns a count of unique populated thoughtwords
	 * @return int
	 */
	public function getWordCount() {
		return $this
			->find('all')
			->select(['word'])
			->distinct(['word'])
			->count();
	}

	/**
	 * Returns a random populated thoughtword
	 * @return string
	 */
	public function getRandomPopulatedThoughtWord() {
		$result = $this
			->select(['word'])
			->order('RAND()')
			->first();
		return $result['Thought']['word'];
	}

	/**
	 * Returns an array of ['first letter' => [words beginning with that letter], ...]
	 * @return array
	 */
	public function getAlphabeticallyGroupedWords() {
		$words = $this->getWords();
		$categorized = array();
		foreach ($words as $word) {
			$first_letter = substr($word, 0, 1);
			 if (is_numeric($first_letter)) {
			 	$categorized['#'][] = $word;
			 } else {
			 	$categorized[$first_letter][] = $word;
			 }
		}
		ksort($categorized);
		return $categorized;
	}

	/**
	 * Used to get paginated thoughts and comments combined
	 * @param Query $query
	 * @param array $options
	 * @return Query
	 */
	public function findRecentActivity(Query $query, array $options) {
		$combined_query = $this->getThoughtsAndComments();
		$limit = $query->clause('limit');
		$offset = $query->clause('offset');
		$direction = isset($_GET['direction']) ? $_GET['direction'] : 'DESC';
		$sort_field = isset($_GET['sort']) ? $_GET['sort'] : 'created';
		$combined_query->epilog("ORDER BY $sort_field $direction LIMIT $limit OFFSET $offset");
		return $combined_query;
	}

	public function getThoughtsAndComments() {
		$thoughts = TableRegistry::get('Thoughts');
		$thoughts_query = $thoughts->find('all');
		$thoughts_query
			->select([
				'created' => 'Thoughts.created',
				'thought_id' => 'Thoughts.id',
				'thought_word' => 'Thoughts.word',
				'thought_anonymous' => 'Thoughts.anonymous',
				'comment_id' => 0
			])
			->contain([
				'Users' => [
					'fields' => ['id', 'color']
				]
			]);

		$comments = TableRegistry::get('Comments');
		$comments_query = $comments
			->find('all')
			->select([
				'created' => 'Comments.created',
				'thought_id' => 'Thoughts.id',
				'thought_word' => 'Thoughts.word',
				'thought_anonymous' => 'Thoughts.anonymous',
				'comment_id' => 'Comments.id'
			])
			->contain([
				'Users' => [
					'fields' => ['id', 'color']
				]
			])
			->join([
				'table' => 'thoughts',
				'alias' => 'Thoughts',
				'conditions' => 'Comments.thought_id = Thoughts.id'
			]);
		return $thoughts_query->unionAll($comments_query);
	}

	/**
	 * Converts $word into a valid thoughtword (alphanumeric, no spaces, max 30 characters)
	 * @param string $word
	 * @return string
	 */
	public function format_thoughtword($word) {
		$word = preg_replace('/[^a-zA-Z0-9]/', '', $word);
		if (strlen($word) > $this->max_thoughtword_length) {
			$word = substr($word, 0, $this->max_thoughtword_length);
		}
		return strtolower($word);
	}

	/**
	 * Checks to see if the thought in $this->request->data is already in the database
	 * @return int|boolean Either the ID of the existing thought or FALSE
	 */
	public function isDuplicate($user_id, $thought) {
		$results = $this
			->findByUserIdAndThought($user_id, $thought)
			->select(['id'])
			->order(['Thought.created' => 'DESC'])
			->first()
			->toArray();
		return isset($results['Thought']['id']) ? $results['Thought']['id'] : false;
	}
}
