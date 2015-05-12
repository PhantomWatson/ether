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
            'className' => 'Users',
            'foreignKey' => 'recipient_id'
        ]);
        $this->belongsTo('Senders', [
            'className' => 'Users',
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

    /**
     * Returns an array of metadata about conversations the user has engaged in
     *
     * @param int $userId
     */
    public function getConversationsIndex($userId)
    {
        $results = $this->find('all')
            ->where([
                'OR' => [
                    'sender_id' => $userId,
                    'recipient_id' => $userId
                ]
            ])
            ->select([
                'sender_id',
                'recipient_id',
                'created'
            ])
            ->distinct(['sender_id'])
            ->contain([
                'Senders' => function ($q) {
                    return $q->select(['id', 'color']);
                },
                'Recipients' => function ($q) {
                    return $q->select(['id', 'color']);
                }
            ])
            ->order(['Messages.created' => 'DESC'])
            ->toArray();

        $conversations = [];
        foreach ($results as $result) {
            $otherUser = ($result['Sender']['id'] == $userId) ? 'Recipient' : 'Sender';
            $otherUserId = $result[$otherUser]['id'];
            if (isset($conversations[$otherUserId])) {
                continue;
            }
            $conversations[$otherUserId] = [
                'color' => $result[$otherUser]['color'],
                'time' => $result['Message']['created']
            ];
        }
        return $conversations;
    }
}
