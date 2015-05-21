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
            ->add('recipient_id', 'valid', ['rule' => 'numeric'])
            ->requirePresence('recipient_id', 'create')
            ->notEmpty('recipient_id')
            ->add('sender_id', 'valid', ['rule' => 'numeric'])
            ->requirePresence('sender_id', 'create')
            ->notEmpty('sender_id')
            ->add('availableToSender', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('availableToSender')
            ->add('availableToRecipient', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('availableToRecipient')
            ->requirePresence('message', 'create')
            ->notEmpty('message');

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
        $query = $this->find('all');
        $results = $query
            ->select([
                'sender_id',
                'recipient_id',
                'created' => $query->func()->max('Messages.created')
            ])
            ->distinct(['sender_id', 'recipient_id'])
            ->contain([
                'Senders' => function ($q) {
                    return $q->select(['id', 'color']);
                },
                'Recipients' => function ($q) {
                    return $q->select(['id', 'color']);
                }
            ])
            ->where([
                'OR' => [
                    'sender_id' => $userId,
                    'recipient_id' => $userId
                ]
            ])
            ->order(['created' => 'DESC'])
            ->toArray();

        $conversations = [];
        foreach ($results as $result) {
            $otherUser = ($result['sender']['id'] == $userId) ? 'recipient' : 'sender';
            $otherUserId = $result[$otherUser]['id'];
            if (isset($conversations[$otherUserId])) {
                continue;
            }
            $conversations[$otherUserId] = [
                'color' => $result[$otherUser]['color'],
                'time' => $result['created']
            ];
        }
        return $conversations;
    }

    public function getConversation($userId, $anotherUserId)
    {
        return $this->find('all')
            ->where([
                'OR' => [
                    [
                        'sender_id' => $userId,
                        'recipient_id' => $anotherUserId
                    ],
                    [
                        'sender_id' => $anotherUserId,
                        'recipient_id' => $userId
                    ]
                ]
            ])
            ->select([
                'id',
                'sender_id',
                'recipient_id',
                'created',
                'message'
            ])
            ->contain([
                'Senders' => function ($q) {
                    return $q->select(['id', 'color']);
                },
                'Recipients' => function ($q) {
                    return $q->select(['id', 'color']);
                }
            ])
            ->order(['Messages.created' => 'ASC'])
            ->toArray();
    }
}
