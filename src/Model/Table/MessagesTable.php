<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Messages Model
 */
class MessagesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('messages');
        $this->displayField('id');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->belongsTo('Recipients', [
            'foreignKey' => 'recipient_id'
        ]);
        $this->belongsTo('Senders', [
            'foreignKey' => 'sender_id'
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
            ->requirePresence('recipient', 'create')
            ->notEmpty('recipient')
            ->add('recipient_id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('recipient_id')
            ->add('availableToRecipient', 'valid', ['rule' => 'numeric'])
            ->requirePresence('availableToRecipient', 'create')
            ->notEmpty('availableToRecipient')
            ->requirePresence('sender', 'create')
            ->notEmpty('sender')
            ->add('sender_id', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('sender_id')
            ->add('availableToSender', 'valid', ['rule' => 'numeric'])
            ->requirePresence('availableToSender', 'create')
            ->notEmpty('availableToSender')
            ->add('status', 'valid', ['rule' => 'numeric'])
            ->requirePresence('status', 'create')
            ->notEmpty('status')
            ->add('timeSent', 'valid', ['rule' => 'numeric'])
            ->requirePresence('timeSent', 'create')
            ->notEmpty('timeSent')
            ->requirePresence('message', 'create')
            ->notEmpty('message')
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
        $rules->add($rules->existsIn(['recipient_id'], 'Recipients'));
        $rules->add($rules->existsIn(['sender_id'], 'Senders'));
        return $rules;
    }

	public function userHasNewMessages($userId)
	{
		$count = $this->find('all')
			->where([
				'recipient_id' => $userId,
				'received' => 0
			])
			->count();
		return $count > 0;
	}
}
