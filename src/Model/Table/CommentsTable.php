<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Comments Model
 */
class CommentsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('comments');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('Thoughts', [
            'foreignKey' => 'thought_id'
        ]);
        $this->belongsTo('Users', [
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
            ->add('thought_id', 'valid', ['rule' => 'numeric'])
            ->requirePresence('thought_id', 'create')
            ->notEmpty('thought_id')
            ->add('user_id', 'valid', ['rule' => 'numeric'])
            ->requirePresence('user_id', 'create')
            ->notEmpty('user_id')
            ->requirePresence('color', 'create')
            ->notEmpty('color')
            ->requirePresence('comment', 'create')
            ->notEmpty('comment')
            ->add('time', 'valid', ['rule' => 'numeric'])
            ->requirePresence('time', 'create')
            ->notEmpty('time')
            ->requirePresence('parsedTextCache', 'create')
            ->notEmpty('parsedTextCache')
            ->add('cacheTimestamp', 'valid', ['rule' => 'numeric'])
            ->requirePresence('cacheTimestamp', 'create')
            ->notEmpty('cacheTimestamp');

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
        $rules->add($rules->existsIn(['thought_id'], 'Thoughts'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        return $rules;
    }
}
